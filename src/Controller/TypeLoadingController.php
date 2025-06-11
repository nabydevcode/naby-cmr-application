<?php

namespace App\Controller;

use App\Entity\TypeLoading;
use App\Form\TypeLoadingType;
use App\Repository\TypeLoadingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class TypeLoadingController extends AbstractController
{
    #[Route('/type/loading', name: 'app_type_loading', methods: ['GET', 'POST'])]
    public function index(Security $security): Response
    {
        $user = $security->getUser();

        if (!$user || !$user->isVerify()) {
            $this->addFlash('error', 'Vous devez vérifier  votre email  ou vous connecter pour accéder à cette page .');
            return $this->redirectToRoute('app_login');
        }
        return $this->render('type_loading/index.html.twig', [
            'controller_name' => 'TypeLoadingController',
        ]);
    }
    #[Route('/type/loading/formulaire', name: 'app_type_loading_formulaire', methods: ['GET', 'POST'])]
    public function formulaire(Request $request, EntityManagerInterface $em, Security $security): Response
    {
        $user = $security->getUser();

        if (!$user || !$user->isVerify()) {
            $this->addFlash('error', 'Vous devez vérifier  votre email  ou vous connecter pour accéder à cette page .');
            return $this->redirectToRoute('app_login');
        }
        $typeloading = new TypeLoading();
        $form = $this->createForm(TypeLoadingType::class, $typeloading);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($typeloading);
            $em->flush();
            return $this->redirectToRoute('app_type_loading');
        }

        return $this->render('type_loading/formulaire.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/type/loading', name: 'app_type_loading', methods: ['GET', 'POST'])]
    public function fullLoadind(Request $request, TypeLoadingRepository $typeLoadingRepository, Security $security): Response
    {

        $user = $security->getUser();

        if (!$user || !$user->isVerify()) {
            $this->addFlash('error', 'Vous devez vérifier  votre email  ou vous connecter pour accéder à cette page .');
            return $this->redirectToRoute('app_login');
        }
        $typeLoad = $typeLoadingRepository->findAll();
        if (!$typeLoad) {
            $this->addFlash('warning', "veillez aujouter un type de chargement ");
            return $this->redirectToRoute('app_type_loading_formulaire');

        }

        return $this->render('type_loading/index.html.twig', ['type_loading' => $typeLoad]);

    }

}
