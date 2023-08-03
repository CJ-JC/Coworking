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
use Dompdf\Dompdf;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

// #[Route('/profil')]
class ProfilUserController extends AbstractController
{

    #[Route('profil/', name: 'app_profil')]
    public function index(ManagerRegistry $doctrine, UserPasswordHasherInterface $userPasswordHasher, Request $request, EntityManagerInterface $entityManager): Response
    {  
        $entityManager = $doctrine->getManager();
        // $workspace = $workspaceRepository->findAll();
        
        /** @var User $user */
        $user = $this->getUser(); // Récupérer l'utilisateur connecté
        // $order = $orderRepository->findBy(['user' => $user]);
        
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
            // 'order' => $order,
            // 'workspace' => $workspace,
            'user' => $user,
            'form' => $form->createView()
        ]);
    }

    #[Route('/profil/reservation', name:'app_reservation')]
    public function reservation(OrderRepository $orderRepository, WorkspaceRepository $workspaceRepository)
    {
        /** @var User $user */
        $user = $this->getUser();

        $workspace = $workspaceRepository->findAll();
        $order = $orderRepository->findBy(['user' => $user]);

        return $this->render('profil_user/reservation.html.twig',[
            'order' => $order,
            'workspace' => $workspace,
        ]);
    }

    #[Route('/profil/delete/{id}', name: 'app_profil_delete')]
    public function deleteReservation(Order $order, ManagerRegistry $doctrine, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
    {
        $entityManager = $doctrine->getManager();

        //Envoi de mail 

        /** @var User $user */
        $user = $this->getUser();

        // $mailer->send($email);
        $htmlContent = $this->renderView('email/annulation.html.twig', [
            'order' => $order,
        ]);

        // Utiliser Dompdf pour générer le fichier PDF
        $dompdf = new Dompdf();
        $dompdf->loadHtml($htmlContent);
        $dompdf->render();
        $pdfContent = $dompdf->output();

        // Créer l'objet Email avec le contenu HTML et l'attachement
        $email = (new Email())
            ->from($user->getEmail())
            ->to('cherley.joachim@gmail.com')
            ->cc($user->getEmail())
            ->subject('Objet : Confirmation de l\'annulation de votre réservation d\'espace de travail')
            ->html($htmlContent);

        $email->attach($pdfContent, 'reservation.pdf', 'application/pdf');
        $mailer->send($email);

        $this->addFlash('success', 'Votre demande d\'annulation de reservation a été envoyée.');
 
        $entityManager->remove($order);
        $entityManager->flush();

        // Rediriger l'utilisateur vers une autre page
        return $this->redirectToRoute('app_profil');
    }

    #[Route('/profil/delete/account/{id}', name: 'app_account_delete')]
    public function deleteAccount(ManagerRegistry $doctrine, EntityManagerInterface $entityManager): Response
    {
        $entityManager = $doctrine->getManager();

        /** @var User $user */
        $user = $this->getUser();

        if ($user->hasNonExpiredReservations()) {
            // Si l'utilisateur a une réservation en cours, affichez un message d'erreur
            $this->addFlash('danger', 'La suppression de votre compte n\'a pas abouti, vous avez une réservation en cours.');
        } else{
            // Si l'utilisateur n'a pas de réservations en cours, on peut le supprimer.
            $entityManager->remove($user);
            // $entityManager->flush();
            
            return $this->redirectToRoute('app_home');
        }

        // Rediriger l'utilisateur vers une autre page
        return $this->redirectToRoute('app_profil');
    }

}

