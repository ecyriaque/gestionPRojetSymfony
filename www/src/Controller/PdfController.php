<?php

namespace App\Controller;

use App\Service\FileManager;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ProjetRepository;
use Symfony\Component\HttpFoundation\Request;

final class PdfController extends AbstractController
{
    #[Route('/note', name: 'app_home_note', methods: ['GET'])]
    public function note(): Response
    {
      
        return $this->render('home/note.html.twig', [
            'logo' => 'data:image/jpg;base64,' . base64_encode(file_get_contents($this->getParameter('kernel.project_dir') . '/public/img/logo.jpg')),
        ]);
    }

    #[Route('/note-pdf', name: 'app_home_note_pdf', methods: ['GET'])]
    public function getPdfNote(FileManager $fm): Response
    {
        $html = $this->renderView('home/note.html.twig', [
            'logo' => 'data:image/jpg;base64,' . base64_encode(file_get_contents($this->getParameter('kernel.project_dir') . '/public/img/logo.jpg')),
        ]);

        $pdf = $fm->streamPdf($html);

        return new Response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="recapitulatif-session.pdf"',
        ]);
    }

    #[Route('/send-note-pdf', name: 'app_home_send_note_pdf', methods: ['GET','POST'])]
    public function sendPdfNote(FileManager $fm, MailerInterface $mailer, LoggerInterface $logger): Response
    {
        $html = $this->renderView('home/note.html.twig', [
            'logo' => 'data:image/jpg;base64,' . base64_encode(file_get_contents($this->getParameter('kernel.project_dir') . '/public/img/logo.jpg')),
        ]);

        $pdf = $fm->streamPdf($html);

        $email = (new Email())
            ->from('no-reply@lesforgesduweb.fr')
            ->to('test@test.test')
            ->subject('Récapitulatif Session')
            ->html('<p>Bonjour,<br>Veuillez trouver ci-joint le récapitulatif de la session.<br>Cordialement</p>')
            ->text("Bonjour\nVeuillez trouver ci-joint le récapitulatif de la session.\nCordialement");

        $email->attach($pdf, 'recapitulatif-session.pdf', 'application/pdf');

        try {
            $mailer->send($email);
            $this->addFlash('success', 'Email envoyé avec succès.');
        } catch (\Symfony\Component\Mailer\Exception\TransportExceptionInterface $e) {
        
            $this->addFlash('error', 'Échec lors de l\'envoi de l\'email : ' . $e->getMessage());

            $logger->error('Mail send failed: ' . $e->getMessage(), ['exception' => $e]);
        }

        return $this->redirectToRoute('home');
    }

    /**
     * Debug route: envoie un email et renvoie le résultat en JSON pour faciliter le debug SMTP
     */
    #[Route('/send-note-pdf-debug', name: 'app_home_send_note_pdf_debug', methods: ['GET'])]
    public function sendPdfNoteDebug(FileManager $fm, MailerInterface $mailer, LoggerInterface $logger): JsonResponse
    {
        $html = $this->renderView('home/note.html.twig', [
            'logo' => 'data:image/jpg;base64,' . base64_encode(@file_get_contents($this->getParameter('kernel.project_dir') . '/public/img/logo.jpg')),
        ]);

        $pdf = $fm->streamPdf($html);

        $email = (new Email())
            ->from('no-reply@lesforgesduweb.fr')
            ->to('test@test.test')
            ->subject('Test envoi PDF')
            ->text('Test envoi PDF via route debug');

        $email->attach($pdf, 'recapitulatif-session.pdf', 'application/pdf');

        try {
            $mailer->send($email);
            return new JsonResponse(['ok' => true, 'message' => 'Email envoyé (MailHog devrait recevoir le message)']);
        } catch (\Symfony\Component\Mailer\Exception\TransportExceptionInterface $e) {
            $logger->error('Mail send failed (debug): ' . $e->getMessage(), ['exception' => $e]);
            return new JsonResponse(['ok' => false, 'error' => 'Transport error: ' . $e->getMessage()], 500);
        } catch (\Throwable $e) {
            $logger->error('Unexpected error (debug): ' . $e->getMessage(), ['exception' => $e]);
            return new JsonResponse(['ok' => false, 'error' => 'Unexpected: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Envoie le récapitulatif PDF au contact facturation du client du projet.
     * URL: /project/{projetid}/send-recap
     */
    #[Route('/project/{projetid}/send-recap', name: 'app_project_send_recap', methods: ['GET','POST'])]
    public function sendRecapToClient(Request $request, int $projetid, ProjetRepository $projetRepo, FileManager $fm, MailerInterface $mailer, LoggerInterface $logger): Response
    {
        $projet = $projetRepo->find($projetid);
        if (!$projet) {
            $this->addFlash('error', 'Projet introuvable.');
            return $this->redirectToRoute('home');
        }

        $client = $projet->getClient();
        if (!$client || !$client->getEmailcontactfacturation()) {
            $this->addFlash('error', 'Aucun contact facturation trouvé pour ce client.');
            return $this->redirectToRoute('home');
        }

        $recipient = $client->getEmailcontactfacturation();
        $cc = [];
        if ($projet->getReferent() && $projet->getReferent()->getEmail()) {
            $cc[] = $projet->getReferent()->getEmail();
        }

        // Render a template with project details (template should handle $projet)
        $html = $this->renderView('home/note.html.twig', [
            'logo' => 'data:image/jpg;base64,' . base64_encode(@file_get_contents($this->getParameter('kernel.project_dir') . '/public/img/logo.jpg')),
            'projet' => $projet,
            'client' => $client,
        ]);

        $pdf = $fm->streamPdf($html);

        $subject = sprintf('Récapitulatif projet : %s', $projet->getNom() ?? '');

        $email = (new Email())
            ->from('no-reply@lesforgesduweb.fr')
            ->to($recipient)
            ->subject($subject)
            ->html($this->renderView('emails/recap_subject.html.twig', ['projet' => $projet, 'client' => $client]))
            ->text(sprintf('Bonjour, vous trouverez en pièce jointe le récapitulatif pour le projet %s.', $projet->getNom()));

        // add CC if present
        foreach ($cc as $ccEmail) {
            $email->addCc($ccEmail);
        }

        $email->attach($pdf, sprintf('recap_projet_%d.pdf', $projet->getProjetid()), 'application/pdf');

        try {
            $mailer->send($email);
            $this->addFlash('success', 'Récapitulatif envoyé au contact facturation.');
        } catch (\Symfony\Component\Mailer\Exception\TransportExceptionInterface $e) {
            $logger->error('Envoi recap projet failed: '.$e->getMessage(), ['exception' => $e]);
            $this->addFlash('error', 'Échec envoi email : ' . $e->getMessage());
        }

        return $this->redirectToRoute('home');
    }
}
