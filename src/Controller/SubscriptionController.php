<?php

namespace App\Controller;

use App\Entity\Subscription;
use App\Form\SubscriptionType;
use App\Repository\SubscriptionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class SubscriptionController extends AbstractController
{
    // #[Route('/subscription', name: 'app_subscription')]
    // public function index(SubscriptionRepository $subscription): Response
    // {
    //     $subscriptions = $subscription->findAll();

    //     return $this->render('subscription/index.html.twig', [
    //         'subscriptions' => $subscriptions
    //     ]);
    // }
}
