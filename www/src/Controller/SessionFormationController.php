<?php

namespace App\Controller;

use App\Entity\SessionFormation;
use App\Form\SessionFormationType;
use App\Repository\SessionFormationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/session/formation')]
final class SessionFormationController extends AbstractController
{
    #[Route(name: 'app_session_formation_index', methods: ['GET'])]
    public function index(SessionFormationRepository $sessionFormationRepository): Response
    {
        return $this->render('session_formation/index.html.twig', [
            'session_formations' => $sessionFormationRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_session_formation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $sessionFormation = new SessionFormation();
        $form = $this->createForm(SessionFormationType::class, $sessionFormation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($sessionFormation);
            $entityManager->flush();

            return $this->redirectToRoute('app_session_formation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('session_formation/new.html.twig', [
            'session_formation' => $sessionFormation,
            'form' => $form,
        ]);
    }

    #[Route('/{sessionid}', name: 'app_session_formation_show', methods: ['GET'])]
    public function show(SessionFormation $sessionFormation): Response
    {
        return $this->render('session_formation/show.html.twig', [
            'session_formation' => $sessionFormation,
        ]);
    }

    #[Route('/{sessionid}/edit', name: 'app_session_formation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, SessionFormation $sessionFormation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SessionFormationType::class, $sessionFormation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_session_formation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('session_formation/edit.html.twig', [
            'session_formation' => $sessionFormation,
            'form' => $form,
        ]);
    }

    #[Route('/{sessionid}', name: 'app_session_formation_delete', methods: ['POST'])]
    public function delete(Request $request, SessionFormation $sessionFormation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$sessionFormation->getSessionid(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($sessionFormation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_session_formation_index', [], Response::HTTP_SEE_OTHER);
    }
}
