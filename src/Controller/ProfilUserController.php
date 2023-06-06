<?php

namespace App\Controller;

use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use App\Form\UserProfilType;
use Doctrine\ORM\EntityManagerInterface;

class ProfilUserController extends AbstractController
{

    #[Route('/profil', name: 'app_profil')]
    public function index(AuthorizationCheckerInterface $authorizationChecker,Request $request, ManagerRegistry $doctrine, EntityManagerInterface $entityManager): Response
    {
        if ($authorizationChecker->isGranted('ROLE_USER')) {

            $user = $this->getUser(); // Récupérer l'utilisateur connecté
           
            $form = $this->createForm(UserProfilType::class, $user);
            $form->handleRequest($request);
    
            if ($form->isSubmitted() && $form->isValid()) {

                $user =$form->getData();
                
                $entityManager = $doctrine->getManager();
                $entityManager->persist($user);
                $entityManager->flush();
    
                // Rediriger ou afficher un message de succès
                return $this->redirectToRoute('app_profil');
            }
            
            return $this->render('profil_user/index.html.twig', [
                'controller_name' => 'ProfilUserController',
                'user' => $user,
                'form' => $form->createView()
            
            ]);
        }
    }
}

