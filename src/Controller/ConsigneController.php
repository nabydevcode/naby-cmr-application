<?php

namespace App\Controller;

use App\Entity\Consigne;
use App\Form\ConsigneType;
use App\Repository\ConsigneRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ConsigneController extends AbstractController
{
    #[Route('/consigne', name: 'app_consigne', methods: ['GET', 'POST'])]
    public function index(Security $security): Response
    {

        $user = $security->getUser();

        if (!$user || !$user->isVerify()) {
            $this->addFlash('error', 'Vous devez vérifier  votre email  ou vous connecter pour accéder à cette page .');
            return $this->redirectToRoute('app_login');
        }
        $this->denyAccessUnlessGranted('ROLE_USER');
        return $this->render(
            'consigne/index.html.twig'
        );
    }
    #[Route('/consigne/formulaire', name: 'consigne_formulaire', methods: ['GET', 'POST'])]
    public function formulaire(Request $request, EntityManagerInterface $em, Security $security): Response
    {

        $user = $security->getUser();

        if (!$user || !$user->isVerify()) {
            $this->addFlash('error', 'Vous devez vérifier  votre email  ou vous connecter pour accéder à cette page .');
            return $this->redirectToRoute('app_login');
        }
        $consigne = new Consigne();
        $form = $this->createForm(ConsigneType::class, $consigne);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($consigne);
            $em->flush();
            return $this->redirectToRoute('app_consigne');
        }
        return $this->render('consigne/formulaire.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/consigne', name: 'app_consigne', methods: ['GET', 'POST'])]

    public function consigne(ConsigneRepository $consigne, Security $security): Response
    {
        $user = $security->getUser();

        if (!$user || !$user->isVerify()) {
            $this->addFlash('error', 'Vous devez vérifier  votre email  ou vous connecter pour accéder à cette page .');
            return $this->redirectToRoute('app_login');
        }
        $consi = $consigne->findAll();

        if (!$consi) {
            $this->addFlash('warning', "veiller ajouter un Destinateur ");
            return $this->redirectToRoute('consigne_formulaire');
        }

        return $this->render('consigne/index.html.twig', ['consigne' => $consi]);

    }
    #[Route('/consigne/update/{id}', name: 'app_consigne_update', methods: ['GET', 'POST'])]
    public function update(Consigne $consigne, EntityManagerInterface $entityManagerInterface, Request $request, Security $security): Response
    {
        $user = $security->getUser();

        if (!$user || !$user->isVerify()) {
            $this->addFlash('error', 'Vous devez vérifier  votre email  ou vous connecter pour accéder à cette page .');
            return $this->redirectToRoute('app_login');
        }
        $form = $this->createForm(ConsigneType::class, $consigne);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManagerInterface->flush();
            $this->addFlash("success", " Destinateur Modifier avec success");
            return $this->redirectToRoute('app_consigne');
        }
        return $this->render('consigne/update.html.twig', [
            'form' => $form->createView(),
            'consigne' => $consigne
        ]);
    }
}
