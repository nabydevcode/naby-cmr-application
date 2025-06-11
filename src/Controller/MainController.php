<?php

namespace App\Controller;

use App\Entity\Shipment;

use App\Repository\ShipmentRepository;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Entity\Users;
use App\Form\RechercheType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use App\Form\ShipmentType;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\PdfServices;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;




use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Spatie\Browsershot\Browsershot;

use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherAwareInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;



class MainController extends AbstractController
{


    public function __construct(private RequestStack $requestStack)
    {

    }
    #[Route('/main', name: 'app_main')]

    public function index(Security $security): Response
    {

        /* $user = $security->getUser();

        if (!$user || !$user->isVerify()) {
            $this->addFlash('error', 'Vous devez vérifier  votre email  ou vous connecter pour accéder à cette page .');
            return $this->redirectToRoute('app_login');
        } */
        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
        ]);

    }

    /*  formulaire pour creer le cmr  */

    #[Route('/main/formulaire', name: 'app_main_formulaire', methods: ['GET', 'POST'])]

    public function formulaire(Request $request, EntityManagerInterface $em, Security $security): Response
    {
        $user = $security->getUser();

        if (!$user || !$user->isVerify()) {
            $this->addFlash('error', 'Vous devez vérifier  votre email  ou vous connecter pour accéder à cette page .');
            return $this->redirectToRoute('app_login');
        }
        $this->denyAccessUnlessGranted('ROLE_USER');

        $shipment = new Shipment();

        $form = $this->createForm(ShipmentType::class, $shipment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $shipment->setCreator($user);
            $em->persist($shipment);
            $em->flush();

            $this->addFlash('success', "CMR creer avec succes ");
            return $this->redirectToRoute('shipment_show', ['id' => $shipment->getId()]);
        }
        return $this->render('main/formulaire.html.twig', ['form' => $form->createView()]);
    }

    /*  pour afficher le cmr creer  en fonction de L'ID donner   */
    #[Route('/shipment/{id}', name: 'shipment_show', methods: ['GET', 'POST'])]

    public function show(Shipment $shipment, Security $security): Response
    {

        $user = $security->getUser();
        if (!$user || !$user->isVerify()) {
            $this->addFlash('error', 'Vous devez vérifier  votre email  ou vous connecter pour accéder à cette page .');
            return $this->redirectToRoute('app_login');
        }
        $this->denyAccessUnlessGranted('ROLE_USER');
        return $this->render('main/show.html.twig', ['shipment' => $shipment]);
    }
    #[Route('/shipment/update/{id}', name: 'shipment_update', methods: ['GET', 'POST'])]


    public function update(Shipment $shipment, Request $request, EntityManagerInterface $em, Security $security): Response
    {

        $user = $security->getUser();
        if (!$user || !$user->isVerify()) {
            $this->addFlash('error', 'Vous devez vérifier  votre email  ou vous connecter pour accéder à cette page .');
            return $this->redirectToRoute('app_login');
        }
        $this->denyAccessUnlessGranted('ROLE_USER');
        $form = $this->createForm(ShipmentType::class, $shipment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', "votre CMR  a ete modifier avec success");
            return $this->redirectToRoute('shipment_show', ['id' => $shipment->getId()]);
        }
        return $this->render('main/update.html.twig', [
            'form' => $form->createView(),
            'shipment' => $shipment
        ]);

    }
    #[Route('/shipment/print/cmr/{id}', name: 'shipment_pdf')]
    public function generatePdf(Shipment $shipment, Security $security): Response
    {
        $user = $security->getUser();
        if (!$user || !$user->isVerify()) {
            $this->addFlash('error', 'Vous devez vérifier  votre email  ou vous connecter pour accéder à cette page .');
            return $this->redirectToRoute('app_login');
        }
        $this->denyAccessUnlessGranted('ROLE_USER');
        $url = $this->generateUrl('shipment_print_pdf', ['id' => $shipment->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        $filePath = $this->getParameter('kernel.project_dir') . '/public/shipment_' . $shipment->getId() . '.pdf';
        /*  Browsershot::url($url)
             ->setNodeBinary('/opt/homebrew/bin/node')
             ->setNpmBinary('/opt/homebrew/bin/npm')
             ->waitUntilNetworkIdle()
             ->format('A4')
             ->save($filePath); */
        Browsershot::url($url)
            ->setNodeBinary('/usr/bin/node')
            ->setNpmBinary('/usr/bin/npm')
            ->setChromePath('/usr/bin/google-chrome')
            ->waitUntilNetworkIdle()
            ->format('A4')
            ->save($filePath);

        $this->addFlash('success', "votre CMR a ete creer avec success ");
        return $this->file($filePath, 'shipment_' . $shipment->getId() . '.pdf', ResponseHeaderBag::DISPOSITION_INLINE);
    }



    /* le pdf prete a imprimer   */
    #[Route('/shipment/pdf/{id}', name: 'shipment_print_pdf', methods: ['GET', 'POST'])]

    public function printPdf(Shipment $shipment): Response
    {

        /* 
               if (!$user ) {
                   $this->addFlash('warning', " Vous devez être connecté pour accéder à cette ressource.");
                   return $this->redirectToRoute('app_login');
               }  */
        return $this->render('main/cmr.html.twig', ['shipment' => $shipment]);
    }



    #[Route('/main/plomb', name: 'main_plomb', methods: ['GET'])]
    public function listShipment(Security $security, EntityManagerInterface $entityManagerInterface): Response
    {
        $user = $security->getUser();
        if (!$user || !$user->isVerify()) {
            $this->addFlash('error', 'Vous devez vérifier  votre email  ou vous connecter pour accéder à cette page .');
            return $this->redirectToRoute('app_login');
        }
        $this->denyAccessUnlessGranted('ROLE_USER');
        $shipment = $entityManagerInterface->getRepository(Shipment::class)->findBy(['creator' => $user]);


        return $this->render('main/list.html.twig', ['shipments' => $shipment]);
    }

    #[Route('/plomb', name: 'general_plomb', methods: ['GET'])]
    public function plomb(Security $security, ShipmentRepository $shipmentRepository): Response
    {

        $user = $security->getUser();

        // Vérifie si l'utilisateur est connecté et a vérifié son email
        if (!$user || !$user->isVerify()) {
            /*  $this->addFlash('error', 'Vous devez vérifier votre email ou vous connecter pour accéder à cette page.'); */
            return $this->redirectToRoute('app_login');
        }
        // Vérifie que l'utilisateur a le rôle ADMIN
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // Récupère tous les Shipments
        $plomb = $shipmentRepository->findAll();

        // Correction du rendu du template
        return $this->render('main/plomb.html.twig', [
            'plombs' => $plomb // Correction de la clé du tableau
        ]);
    }




}