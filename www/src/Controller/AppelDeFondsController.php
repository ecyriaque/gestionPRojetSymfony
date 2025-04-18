<?php

namespace App\Controller;

use App\Entity\AppelDeFonds;
use App\Form\AppelDeFondsType;
use App\Repository\AppelDeFondsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/appel/de/fonds')]
final class AppelDeFondsController extends AbstractController
{
    #[Route(name: 'app_appel_de_fonds_index', methods: ['GET'])]
    public function index(AppelDeFondsRepository $appelDeFondsRepository): Response
    {
        return $this->render('appel_de_fonds/index.html.twig', [
            'appel_de_fonds' => $appelDeFondsRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_appel_de_fonds_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $appelDeFond = new AppelDeFonds();
        $form = $this->createForm(AppelDeFondsType::class, $appelDeFond);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($appelDeFond);
            $entityManager->flush();

            return $this->redirectToRoute('app_appel_de_fonds_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('appel_de_fonds/new.html.twig', [
            'appel_de_fond' => $appelDeFond,
            'form' => $form,
        ]);
    }

    #[Route('/{appelid}', name: 'app_appel_de_fonds_show', methods: ['GET'])]
    public function show(AppelDeFonds $appelDeFond): Response
    {
        return $this->render('appel_de_fonds/show.html.twig', [
            'appel_de_fond' => $appelDeFond,
        ]);
    }

    #[Route('/{appelid}/edit', name: 'app_appel_de_fonds_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, AppelDeFonds $appelDeFond, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AppelDeFondsType::class, $appelDeFond);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_appel_de_fonds_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('appel_de_fonds/edit.html.twig', [
            'appel_de_fond' => $appelDeFond,
            'form' => $form,
        ]);
    }

    #[Route('/{appelid}', name: 'app_appel_de_fonds_delete', methods: ['POST'])]
    public function delete(Request $request, AppelDeFonds $appelDeFond, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$appelDeFond->getAppelid(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($appelDeFond);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_appel_de_fonds_index', [], Response::HTTP_SEE_OTHER);
    }
}
