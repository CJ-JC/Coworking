<?php

namespace App\Controller;

use App\Form\ResetPasswordUserType;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use App\Form\UserProfilType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ProfilUserController extends AbstractController
{

    #[Route('/profil', name: 'app_profil')]
    public function index(AuthorizationCheckerInterface $authorizationChecker, UserPasswordHasherInterface $userPasswordHasher, Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($authorizationChecker->isGranted('ROLE_USER')) {

            $user = $this->getUser();
           
            $formPassword = $this->createForm(ResetPasswordUserType::class, $user);
            $formPassword->handleRequest($request);

            $form = $this->createForm(UserProfilType::class, $user);
            $form->handleRequest($request);
    
            if ($form->isSubmitted() && $form->isValid()) {

                $user = $form->getData();
                
                $entityManager->persist($user);
                $entityManager->flush();
    
                return $this->redirectToRoute('app_profil');
            }

            if ($formPassword->isSubmitted() && $formPassword->isValid()) {
                $user->setPassword($userPasswordHasher->hashPassword($user, $user->getPassword()));

                $entityManager->persist($user);
                $entityManager->flush();
    
                $this->addFlash('success', 'Votre mot de passe a été modifié avec succès.');
    
                return $this->redirectToRoute('app_profil');
            }
            
            return $this->render('profil_user/index.html.twig', [
                'user' => $user,
                'form' => $form->createView(),
                'formPassword' => $formPassword->createView()
            ]);
        }
    }
}

