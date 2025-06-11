<?php

namespace App\Controller;

use App\Entity\Transporteur;
use App\Form\TransporteurType;
use App\Repository\TransporteurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class TransporteurController extends AbstractController
{
    #[Route('/transporteur', name: 'app_transporteur', methods: ['GET', 'POST'])]
    public function index(Security $security): Response
    {
        $user = $security->getUser();

        if (!$user || !$user->isVerify()) {
            $this->addFlash('error', 'Vous devez vérifier  votre email  ou vous connecter pour accéder à cette page .');
            return $this->redirectToRoute('app_login');
        }
        return $this->render('transporteur/index.html.twig', [
            'controller_name' => 'TransporteurController',
        ]);
    }
    #[Route('/transporteur/formulaire', name: 'app_transporteur_formulaire', methods: ['GET', 'POST'])]
    public function formulaire(Request $request, EntityManagerInterface $em, Security $security): Response
    {

        $user = $security->getUser();

        if (!$user || !$user->isVerify()) {
            $this->addFlash('error', 'Vous devez vérifier  votre email  ou vous connecter pour accéder à cette page .');
            return $this->redirectToRoute('app_login');
        }
        $transport = new Transporteur();
        $form = $this->createForm(TransporteurType::class, $transport);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($transport);
            $em->flush();
            return $this->redirectToRoute('app_transporteur');
        }

        return $this->render('transporteur/formulaire.html.twig', ['form' => $form->createView()]);
    }
    #[Route('/transporteur', name: 'app_transporteur', methods: ['GET', 'POST'])]

    public function tranporteur(TransporteurRepository $transporteurType, Security $security): Response
    {
        $user = $security->getUser();

        if (!$user || !$user->isVerify()) {
            $this->addFlash('error', 'Vous devez vérifier  votre email  ou vous connecter pour accéder à cette page .');
            return $this->redirectToRoute('app_login');
        }

        $tranp = $transporteurType->findAll();
        if (!$tranp) {
            $this->addFlash('warning', "Veiller ajouter un Transporteur ");
            return $this->redirectToRoute('app_transporteur_formulaire');
        }

        return $this->render('transporteur/index.html.twig', ['transporteurs' => $tranp]);
    }

    #[Route('/transporteur/update/{id}', name: 'app_transporteur_update', methods: ['GET', 'POST'])]
    public function update(Request $request, Transporteur $transporteur, EntityManagerInterface $em, Security $security): Response
    {
        $user = $security->getUser();

        if (!$user || !$user->isVerify()) {
            $this->addFlash('error', 'Vous devez vérifier  votre email  ou vous connecter pour accéder à cette page .');
            return $this->redirectToRoute('app_login');
        }
        $form = $this->createForm(TransporteurType::class, $transporteur);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', "Votre transporteur a été modifier avec succcess");
            return $this->redirectToRoute('app_transporteur');
        }
        return $this->render('transporteur/update.html.twig', [
            'form' => $form->createView(),
            'transporteur' => $transporteur
        ]);
    }
    /* #[Route('/transporteur/delet/{id}', name: 'app_transporteur_delette', methods: ['GET', 'POST'])]
    public function delet(Request $request, Transporteur $transporteur, EntityManagerInterface $em): Response
    {
        if ($transporteur) {

            $em->remove($transporteur);
            $em->flush();
            $this->addFlash('success', "transporteur supprimer avec success");
            return $this->redirectToRoute('app_transporteur');


        }
        $this->addFlash('warning', " cet transporteur n'existe pas ");
        return $this->redirectToRoute('app_transporteur');
    } */





}
