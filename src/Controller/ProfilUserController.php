<?php

namespace App\Controller;


use App\Form\ResetPasswordUserType;
use App\Entity\CategoryWorkspace;
use App\Entity\Order;
use App\Entity\Subscription;
use App\Entity\Workspace;

use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use App\Form\UserProfilType;
use App\Repository\CategoryWorkspaceRepository;
use App\Repository\OrderRepository;
use App\Repository\SubscriptionRepository;
use App\Repository\WorkspaceRepository;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class ProfilUserController extends AbstractController
{

    #[Route('/profil', name: 'app_profil')]
    public function index(AuthorizationCheckerInterface $authorizationChecker, UserPasswordHasherInterface $userPasswordHasher, Request $request, EntityManagerInterface $entityManager): Response
    public function index(AuthorizationCheckerInterface $authorizationChecker,Request $request, 
    ManagerRegistry $doctrine, EntityManagerInterface $entityManager,OrderRepository $orderRepository,
    WorkspaceRepository $workspaceRepository, SubscriptionRepository $subscriptionRepository): Response
    {

        $entityManager = $doctrine->getManager();
        $orderRepository = $entityManager->getRepository(Order::class);
        $order = $orderRepository->findAll();

        $entityManager = $doctrine->getManager();
        $workspaceRepository = $entityManager->getRepository(Workspace::class);
        $workspace = $workspaceRepository->findAll();

        $entityManager = $doctrine->getManager();
        $subscriptionRepository = $entityManager->getRepository(Subscription::class);
        $subscription = $subscriptionRepository->findAll();

        if ($authorizationChecker->isGranted('ROLE_USER')) {

            $user = $this->getUser(); // Récupérer l'utilisateur connecté
           
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
        
        return $this->render('profil_user/index.html.twig', [
            'controller_name' => 'ProfilUserController',
            'order' => $order,
            'workspace' => $workspace,
            'user' => $user,
            'subscription' => $subscription,
            'form' => $form->createView()
        
        ]);
    }


    #[Route('/profil/{id}', name: 'app_profil_delete')]
    public function deleteElement(Order $element, ManagerRegistry $doctrine, 
    EntityManagerInterface $entityManager, MailerInterface $mailer): Response
    {
        $entityManager = $doctrine->getManager();

        //procedure mail 

        $email = (new Email())
         ->from($this->getUser()->getEmail())
        ->to('contact@gusto.com')
        ->subject('Annulation de reservation')
        ->html('Cette utilisateur a annulé sa reservation'.$this->getUser()->getFirstname());


    $mailer->send($email);

    $this->addFlash('success', 'Votre message a été envoyé');
 
        $entityManager->remove($element);
        $entityManager->flush();

        // Rediriger l'utilisateur vers une autre page
        return $this->redirectToRoute('app_profil');
    }
}

