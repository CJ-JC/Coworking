<?php

namespace App\Controller;

use App\Entity\Subscription;
use App\Form\SubscriptionType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class SubscriptionController extends AbstractController
{
    #[Route('/subscription', name: 'app_subscription')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $subscription = new Subscription();

        // if ($this->getUser()) {
        //     return $this->redirectToRoute('app_login');
        // }

        $form = $this->createForm(SubscriptionType::class, $subscription);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            
            $entityManager->persist($subscription);
            $entityManager->flush();
        }

        return $this->render('subscription/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
