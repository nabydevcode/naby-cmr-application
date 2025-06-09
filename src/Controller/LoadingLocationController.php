<?php

namespace App\Controller;


use App\Entity\LoadingLocations;
use App\Form\LoadingLocationType;
use App\Repository\LoadingLocationsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class LoadingLocationController extends AbstractController
{
    #[Route('/loading/location', name: 'app_loading_location', methods: ['GET', 'POST'])]
    public function index(LoadingLocationsRepository $loadingLocationsRepository): Response
    {

        $loading = $loadingLocationsRepository->findAll();
        if (!$loading) {
            $this->addFlash('warning', "il ya aucun lieux enregistrer ");
            return $this->redirectToRoute('app_loading_formualaire');
        }
        return $this->render('loading_location/index.html.twig', [
            'loading' => $loading
        ]);
    }
    #[Route('/loading/location/update/{id}', name: 'app_loading_update', methods: ['GET', 'POST'])]

    public function update(LoadingLocations $loadingLocations, EntityManagerInterface $entityManagerInterface, Request $request): Response
    {
        $form = $this->createForm(LoadingLocationType::class, $loadingLocations);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManagerInterface->flush();
            $this->addFlash('success', "Modifications effectuer avec success");
            return $this->redirectToRoute('app_loading_location');
        }

        return $this->render('loading_location/update.html.twig', ['form' => $form->createView()]);
    }


    #[Route('/loading/formulaire', name: 'app_loading_formualaire', methods: ['GET', 'POST'])]
    public function formulaire(Request $request, EntityManagerInterface $em): Response
    {
        $loading = new LoadingLocations();
        $form = $this->createForm(LoadingLocationType::class, $loading);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $em->persist($loading);
            $em->flush();
            return $this->redirectToRoute('app_loading_location');
        }


        return $this->render('loading_location/formulaire.html.twig', ['form' => $form->createView()]);
    }

}
