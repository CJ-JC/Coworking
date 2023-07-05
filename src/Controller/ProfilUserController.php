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
    public function index(ManagerRegistry $doctrine, UserPasswordHasherInterface $userPasswordHasher, Request $request, EntityManagerInterface $entityManager, OrderRepository $orderRepository, WorkspaceRepository $workspaceRepository): Response
    {
        
        $entityManager = $doctrine->getManager();
        $workspace = $workspaceRepository->findAll();
        
        /** @var User $user */
        $user = $this->getUser(); // Récupérer l'utilisateur connecté
        $order = $orderRepository->findBy(['user' => $user]);
        
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
            'formPassword' => $formPassword->createView(),
            'order' => $order,
            'workspace' => $workspace,
            'user' => $user,
            'form' => $form->createView()
        ]);
    }


    #[Route('/profil/{id}', name: 'app_profil_delete')]
    public function deleteReservation(Order $order, ManagerRegistry $doctrine, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
    {
        $entityManager = $doctrine->getManager();

        //Envoi de mail 

        /** @var User $user */
        $user = $this->getUser();
        $email = (new Email())
            ->from($user->getEmail())
            ->to('contact@gusto.com')
            ->cc($user->getEmail())
            ->subject('Annulation de reservation')
            ->html('Cette utilisateur a annulé sa reservation'.' '.$user->getFirstname());

        $mailer->send($email);

        $this->addFlash('success', 'Votre message a été envoyé');
 
        $entityManager->remove($order);
        $entityManager->flush();

        // Rediriger l'utilisateur vers une autre page
        return $this->redirectToRoute('app_profil');
    }
}

