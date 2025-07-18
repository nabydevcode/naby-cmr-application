<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\ChangePasswordType;
use App\Form\RegistrationForm;
use App\Form\ResetPassowrdFormType;
use App\Form\ResetPassowrdRequestFormTypeForm;
use App\Form\SearchEmailType;
use App\Repository\UsersRepository;
use App\Security\AppAuthenticatorsAuthenticator;
use App\Security\AppUsersAuthenticator;
use App\Service\JWTServices;
use App\Service\SendMailServices;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class RegistrationController extends AbstractController
{

    private ParameterBagInterface $params;

    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
    }
    #[Route('/register', name: 'app_register')]

    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        Security $security,
        EntityManagerInterface $entityManager,
        SendMailServices $sendMailServices,
        JWTServices $jwt
    ): Response {
        $user = new Users();
        $form = $this->createForm(RegistrationForm::class, $user);
        $form->handleRequest($request);

        // ✅ On vérifie d'abord que le formulaire a été soumis et est valide
        if ($form->isSubmitted() && $form->isValid()) {
            // ✅ Récupération du token reCAPTCHA depuis le formulaire



            // ✅ Création du compte utilisateur
            $plainPassword = $form->get('plainPassword')->getData();
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            $entityManager->persist($user);
            $entityManager->flush();

            // ✅ Génération du token JWT
            $header = ['typ' => 'JWT', 'alg' => 'HS256'];
            $payload = ['user_id' => $user->getId()];
            $token = $jwt->generate($header, $payload, $this->getParameter('app.jwtsecret'));

            // ✅ Envoi de l’email d’activation
            $sendMailServices->send(
                'nabytoure-admin@nabytoure.com',
                $user->getEmail(),
                'Activation de votre compte sur le site nabytoure.com(CmrT)',
                'register',
                compact('user', 'token')
            );

            $this->addFlash('warning', "📬 Veuillez vérifier votre email pour valider votre compte.");

            // ✅ Connexion immédiate
            return $security->login($user, AppUsersAuthenticator::class, 'main');
        }

        // 👇 Rendu du formulaire avec la clé reCAPTCHA
        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,

        ]);
    }


    #[Route('/verif/{token}', name: 'verify_user')]
    public function verify($token, JWTServices $jwt, UsersRepository $usersRepository, EntityManagerInterface $em, ): Response
    {
        //on verifie si le token est valide , n'a pas expiré et n'a pas été modifié

        if (
            $jwt->isValid($token) && !$jwt->isExpired($token) && $jwt->check(
                $token,
                $this->getParameter('app.jwtsecret')
            )
        ) {
            // on recupere le payload 
            $payload = $jwt->getPayload($token);
            // on recupere le user du token

            $user = $usersRepository->find($payload['user_id']);

            //on verifie que l'utilisateur existe et n'a pas encore active son compte
            if ($user && !$user->isVerify()) {
                $user->setIsVerify(true);
                $em->flush();

                $this->addFlash('success', "Utilisateur Activé ");
                return $this->redirectToRoute('app_main');
            }
        }
        // ici un probleme se pose dans le token 

        $this->addFlash('danger', 'le token est invalide ou a expiré');

        return $this->redirectToRoute('app_login');

    }
    #[Route('/renvoiverif', name: 'resend_verif')]
    public function resendVerif(JWTServices $jwt, SendMailServices $mail, UsersRepository $usersRepository): Response
    {

        $user = $this->getUser();

        if (!$user) {
            $this->addFlash('warning', 'Votre devez être connecté pour acceder a cette page ');
            return $this->redirectToRoute('app_login');
        }

        if ($user->isVerify()) {
            $this->addFlash('warning', 'Cet utilisateur est deja activé');
            return $this->redirectToRoute('app_main');

        }

        $header = [

            'typ' => 'JWT',
            'alg' => 'HS256'
        ];
        // ON creer le payload
        $payload = [
            'user_id' => $user->getId()
        ];
        // on genere le token
        $token = $jwt->generate($header, $payload, $this->getParameter('app.jwtsecret'));

        // do anything else you need here, like send an email
        $mail->send(
            'nabytoure-admin@nabytoure.com',
            $user->getEmail(),
            'Activation de votre compte sur le site nabytoure.com(CmrT)',
            'register',
            compact('user', 'token')
        );

        $this->addFlash('success', 'Email de vérification envoyé ');
        return $this->redirectToRoute('app_main');
    }

    #[Route('/oubli-pass', name: 'forgotten_password')]
    public function forgottenPassword(
        Request $request,
        TokenGeneratorInterface $tokenGenerator
        ,
        UsersRepository $usersRepository,
        EntityManagerInterface $entityManager,
        SendMailServices $mail
    ): Response {


        $form = $this->createForm(ResetPassowrdRequestFormTypeForm::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $usersRepository->findByEmail($form->get('email')->getData());

            if ($user) {
                $token = $tokenGenerator->generateToken();

                $user->setTokenUser($token);
                $entityManager->persist($user);
                $entityManager->flush();
                $url = $this->generateUrl('reset_pass', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);

                //on crée les données du mail
                $context = compact('url', 'user');
                //on envoi  mail 

                $mail->send(
                    'nabytoure-admin@nabytoure.com',

                    $user->getEmail(),
                    'Réinitialisation de mot de pass',
                    'password_reset',
                    $context
                );
                $this->addFlash('success', 'Email envoyé avec succès');
                return $this->redirectToRoute('app_login');




            } else {

                $this->addFlash('error', "Un probleme est survenu");
                return $this->redirectToRoute('app_login');
            }
        }



        return $this->render('security/reset-password.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/oubli-pass/{token}', name: 'reset_pass')]
    public function resetPass(Request $request, UsersRepository $usersRepository, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
        $token = $request->attributes->get('token'); // Récupère le token depuis l'URL
        $user = $usersRepository->findOneByTokenUser($token);

        if ($user) {
            $form = $this->createForm(ResetPassowrdFormType::class, $user);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $newPassword = $form->get('password')->getData();
                var_dump($newPassword);
                $user->setPassword($passwordHasher->hashPassword($user, $newPassword));
                $user->setTokenUser(''); // Supprime le token après réinitialisation

                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('success', 'Votre mot de passe a été mis à jour.');
                return $this->redirectToRoute('app_login');
            }

        } else {

            $this->addFlash('warning', "Un problème est survenu");
            return $this->redirectToRoute('app_login');

        }


        return $this->render('security/reset.html.twig', [
            'form' => $form->createView()
        ]);
    }


}
