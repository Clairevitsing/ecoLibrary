<?php

namespace App\Controller;

use App\Entity\StatusCommande;
use App\Form\StatusCommandeType;
use App\Repository\StatusCommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/status/commande')]
class StatusCommandeController extends AbstractController
{
    #[Route('/', name: 'app_status_commande_index', methods: ['GET'])]
    public function index(StatusCommandeRepository $statusCommandeRepository): Response
    {
        return $this->render('status_commande/index.html.twig', [
            'status_commandes' => $statusCommandeRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_status_commande_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $statusCommande = new StatusCommande();
        $form = $this->createForm(StatusCommandeType::class, $statusCommande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($statusCommande);
            $entityManager->flush();

            return $this->redirectToRoute('app_status_commande_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('status_commande/new.html.twig', [
            'status_commande' => $statusCommande,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_status_commande_show', methods: ['GET'])]
    public function show(StatusCommande $statusCommande): Response
    {
        return $this->render('status_commande/show.html.twig', [
            'status_commande' => $statusCommande,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_status_commande_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, StatusCommande $statusCommande, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(StatusCommandeType::class, $statusCommande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_status_commande_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('status_commande/edit.html.twig', [
            'status_commande' => $statusCommande,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_status_commande_delete', methods: ['POST'])]
    public function delete(Request $request, StatusCommande $statusCommande, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$statusCommande->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($statusCommande);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_status_commande_index', [], Response::HTTP_SEE_OTHER);
    }
}
