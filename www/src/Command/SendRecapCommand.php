<?php

namespace App\Command;

use App\Repository\ProjetRepository;
use App\Repository\SessionFormationRepository;
use App\Repository\FormationRepository;
use App\Service\FileManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment as TwigEnvironment;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

#[AsCommand(name: 'app:send-recap', description: 'Génère et envoie le récapitulatif PDF pour un projet (dev only)')]
final class SendRecapCommand extends Command
{
    private string $projectDir;

    public function __construct(
        private ProjetRepository $projetRepo,
        private SessionFormationRepository $sessionRepo,
        private FormationRepository $formationRepo,
        private FileManager $fm,
        private MailerInterface $mailer,
        private TwigEnvironment $twig,
        private LoggerInterface $logger,
        ParameterBagInterface $params
    ) {
        $this->projectDir = $params->get('kernel.project_dir');
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('projetid', InputArgument::REQUIRED, 'ID du projet');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (getenv('APP_ENV') !== 'dev') {
            $output->writeln('<error>Commande autorisée uniquement en environnement dev.</error>');
            return Command::FAILURE;
        }

        $projetid = (int) $input->getArgument('projetid');
        $projet = $this->projetRepo->find($projetid);
        if (!$projet) {
            $output->writeln('<error>Projet introuvable.</error>');
            return Command::FAILURE;
        }

        $client = $projet->getClient();
        if (!$client || !$client->getEmailcontactfacturation()) {
            $output->writeln('<error>Aucun contact facturation trouvé pour ce client.</error>');
            return Command::FAILURE;
        }

        $sessions = $this->sessionRepo->findBy(['projet' => $projet]);

        $sessionsWithFormations = [];
        $grandTotalHT = 0.0;
        $grandTotalTVA = 0.0;
        $grandTotalTTC = 0.0;

        foreach ($sessions as $session) {
            $formations = $this->formationRepo->findBy(['session' => $session]);
            $sessionSubtotalHT = 0.0;
            $sessionSubtotalTVA = 0.0;
            $sessionSubtotalTTC = 0.0;
            $formationRows = [];

            foreach ($formations as $f) {
                $ht = (float) $f->getCout();
                $tvaPct = (float) $f->getTauxtva();
                $tvaAmount = $ht * $tvaPct / 100.0;
                $ttc = $ht + $tvaAmount;

                $sessionSubtotalHT += $ht;
                $sessionSubtotalTVA += $tvaAmount;
                $sessionSubtotalTTC += $ttc;

                $formationRows[] = [
                    'entity' => $f,
                    'ht' => $ht,
                    'tvaPct' => $tvaPct,
                    'tvaAmount' => $tvaAmount,
                    'ttc' => $ttc,
                ];
            }

            $grandTotalHT += $sessionSubtotalHT;
            $grandTotalTVA += $sessionSubtotalTVA;
            $grandTotalTTC += $sessionSubtotalTTC;

            $sessionsWithFormations[] = [
                'session' => $session,
                'formations' => $formationRows,
                'subtotal_ht' => $sessionSubtotalHT,
                'subtotal_tva' => $sessionSubtotalTVA,
                'subtotal_ttc' => $sessionSubtotalTTC,
            ];
        }

        $html = $this->twig->render('pdf/recap_projet.html.twig', [
            'logo' => 'data:image/jpg;base64,' . base64_encode(@file_get_contents($this->projectDir . '/public/img/logo.jpg')),
            'projet' => $projet,
            'client' => $client,
            'sessions' => $sessionsWithFormations,
            'grandTotalHT' => $grandTotalHT,
            'grandTotalTVA' => $grandTotalTVA,
            'grandTotalTTC' => $grandTotalTTC,
        ]);

        $pdf = $this->fm->streamPdf($html);

        $subject = sprintf('Récapitulatif projet : %s', $projet->getNom() ?? '');

        $email = (new Email())
            ->from('no-reply@lesforgesduweb.fr')
            ->to($client->getEmailcontactfacturation())
            ->subject($subject)
            ->html($this->twig->render('emails/recap_subject.html.twig', ['projet' => $projet, 'client' => $client]))
            ->text(sprintf('Bonjour, vous trouverez en pièce jointe le récapitulatif pour le projet %s.', $projet->getNom()));

        $email->attach($pdf, sprintf('recap_projet_%d.pdf', $projet->getProjetid()), 'application/pdf');

        $this->logger->info('Sending recap (console)', ['to' => $client->getEmailcontactfacturation(), 'project' => $projetid, 'pdf_size' => is_string($pdf) ? strlen($pdf) : null]);

        try {
            $this->mailer->send($email);
            $output->writeln('<info>Email envoyé avec succès.</info>');
            $this->logger->info('SendRecapCommand: Email sent', ['to' => $client->getEmailcontactfacturation(), 'project' => $projetid]);
            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $this->logger->error('SendRecapCommand failed: ' . $e->getMessage(), ['exception' => $e]);
            $output->writeln('<error>Échec envoi email : ' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }
    }
}
