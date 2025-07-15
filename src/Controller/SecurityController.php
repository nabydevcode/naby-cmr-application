<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\UserRoleTypeForm;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
    #[Route(path: '/dashabord', name: 'app_dasabord')]

    public function deshabord(Security $security, UsersRepository $repos): Response
    {
        $user = $security->getUser();

        // 1. Sécurité : vérifier connexion & rôle
        if (!$user || !$user->isVerify()) {
            return $this->redirectToRoute('app_login');
        }

        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $users = $repos->findAll();

        return $this->render('security/deshabord.html.twig', [
            'users' => $users,
        ]);
    }
    #[Route('/admin/user/{id}/roles', name: 'user_roles_edit')]
    public function editRoles(Security $security, Users $user, Request $request, EntityManagerInterface $em): Response
    {

        $user = $security->getUser();
        // 1. Sécurité : vérifier connexion & rôle
        if (!$user || !$user->isVerify()) {
            return $this->redirectToRoute('app_login');
        }
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createForm(UserRoleTypeForm::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Rôles mis à jour avec succès !');
            return $this->redirectToRoute('app_dasabord');
        }

        return $this->render('security/user_roles.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }
}
