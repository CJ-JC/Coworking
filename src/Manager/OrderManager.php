<?php

namespace App\Manager;

use App\Entity\Order;
use App\Entity\User;
use App\Entity\Workspace;
use App\Service\StripeService;
use Doctrine\ORM\EntityManagerInterface;

class OrderManager
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var StripeService
     */
    protected $stripeService;

    /**
     * @param EntityManagerInterface $entityManager
     * @param StripeService $stripeService
     */
    public function __construct(EntityManagerInterface $entityManager, StripeService $stripeService)
    {
        $this->entityManager = $entityManager;
        $this->stripeService = $stripeService;
    }

    public function intentSecret(Workspace $workspace)
    {
        $intent = $this->stripeService->paymentIntent($workspace);

        return $intent['client_secret'] ?? null;
    }

    /**
     * @param array $stripeParameter
     * @param Workspace $workspace
     * @return array|null
     */
    public function stripe(array $stripeParameter, Workspace $workspace)
    {
        $ressource = null;
        $data = $this->stripeService->stripe($stripeParameter, $workspace);

        if($data) {
            $ressource = [
                // 'stripeBrand' => $data['charges']['data'][0]['payment_method_details']['card']['brand'],
                // 'stripeLast4' => $data['charges']['data'][0]['payment_method_details']['card']['last4'],
                'stripeId' => $data['charges']['data'][0]['id'],
                'stripeStatus' => $data['charges']['data'][0]['status'],
                'stripeToken' => $data['client_secret']
            ];
        }
        return $ressource;
    }

    /**
     * @param array $ressource
     * @param Workspace $worspace
     * @param User $user
     */
    public function create_subscription(array $ressource, Workspace $workspace, User $user)
    {
        $order = new Order();

        $order->setUser($user);
        $order->setWorkspace($workspace);
        $order->setPrice($workspace->getPrice());
        $order->setReference(uniqid('', false));
        $order->setIdChargeStripe($ressource['stripeId']);
        $order->setStripeToken($ressource['stripeToken']);
        $order->setStatusStripe($ressource['stripeStatus']);
        $order->setUpdatedAt(new \DateTimeInterface());
        $order->setCreatedAt(new \DateTimeInterface());

        $this->entityManager->persist($order);
        $this->entityManager->flush();
    }
}