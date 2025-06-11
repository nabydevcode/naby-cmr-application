<?php

namespace App\Controller;

use App\Entity\Company;
use App\Form\CompanyType;
use App\Repository\CompanyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CompanyController extends AbstractController
{
    #[Route('/company', name: 'app_company', methods: ['GET', 'POST'])]
    public function index(Security $security, CompanyRepository $companyRepository): Response
    {

        $user = $security->getUser();
        if (!$user || !$user->isVerify()) {
            $this->addFlash('error', 'Vous devez vérifier  votre email  ou vous connecter pour accéder à cette page .');
            return $this->redirectToRoute('app_login');
        }
        $this->denyAccessUnlessGranted('ROLE_USER');


        $company = $companyRepository->findAll();
        if (!$company) {
            $this->addFlash('warning', "veillez ajouter un Expediteur");
            return $this->redirectToRoute('form_company');
        }
        return $this->render('company/index.html.twig', [
            'company' => $company
        ]);
    }



    #[Route('/formulaire', name: 'form_company')]

    public function formulaire(Security $security, Request $request, EntityManagerInterface $em): Response
    {

        $user = $security->getUser();
        if (!$user || !$user->isVerify()) {
            $this->addFlash('error', 'Vous devez vérifier  votre email  ou vous connecter pour accéder à cette page .');
            return $this->redirectToRoute('app_login');
        }
        $this->denyAccessUnlessGranted('ROLE_USER');
        $company = new Company();
        $form = $this->createForm(CompanyType::class, $company);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($company);
            $em->flush();
            return $this->redirectToRoute('app_company');
        }
        return $this->render('company/formulaire.html.twig', ['form' => $form->createView()]);

    }
    #[Route('/company/update/{id}', name: 'company_update')]
    public function update(Security $security, Company $company, EntityManagerInterface $entityManagerInterface, Request $request): Response
    {
        $user = $security->getUser();
        if (!$user || !$user->isVerify()) {
            $this->addFlash('error', 'Vous devez vérifier  votre email  ou vous connecter pour accéder à cette page .');
            return $this->redirectToRoute('app_login');
        }
        $this->denyAccessUnlessGranted('ROLE_USER');

        if (!$company) {
            $this->addFlash('warning', " Cet destinateur n'existe pas ");
            return $this->redirectToRoute('app_company');
        }
        $form = $this->createForm(CompanyType::class, $company);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManagerInterface->flush();
            $this->addFlash('success', "Destinateur modifier avec success");
            return $this->redirectToRoute('app_company');
        }
        return $this->render('company/update.html.twig', ['form' => $form->createView()]);

    }


}
