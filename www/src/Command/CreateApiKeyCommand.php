<?php

namespace App\Command;

use App\Entity\ApiKey;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:create-api-key', description: 'Génère une nouvelle clé API')]
class CreateApiKeyCommand extends Command
{
    public function __construct(private EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('name', InputArgument::REQUIRED, 'Nom de l\'application cliente')
            ->addOption('ip', null, InputOption::VALUE_OPTIONAL, 'IP autorisée (optionnel, pour filtrage futur)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $rawKey = bin2hex(random_bytes(32));
        $hash   = hash('sha256', $rawKey);

        $apiKey = new ApiKey();
        $apiKey->setName($input->getArgument('name'));
        $apiKey->setKeyHash($hash);
        $apiKey->setAllowedIp($input->getOption('ip'));

        $this->em->persist($apiKey);
        $this->em->flush();

        $io->success('Clé API créée avec succès. Copiez-la maintenant, elle ne sera plus affichée.');
        $io->table(['Champ', 'Valeur'], [
            ['Nom', $apiKey->getName()],
            ['Clé API (raw)', $rawKey],
            ['IP autorisée', $apiKey->getAllowedIp() ?? 'Toutes'],
            ['Créée le', $apiKey->getCreatedAt()->format('Y-m-d H:i:s')],
        ]);

        return Command::SUCCESS;
    }
}
