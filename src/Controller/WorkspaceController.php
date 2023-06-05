<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Subscription;
use App\Entity\User;
use App\Entity\Workspace;
use App\Form\OrderType;
use App\Repository\SubscriptionRepository;
use App\Repository\WorkspaceRepository;
use App\Service\CountPlaceService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;


#[Route('/workspace')]
class WorkspaceController extends AbstractController
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    #[Route('/{id}', name: 'app_workspace_show')]
    public function show(Workspace $workspaces, Request $request, EntityManagerInterface $entityManager, SubscriptionRepository $subscription): Response
    {
        $order = new Order();

        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);

        $subscriptions = $subscription->findAll();

        if ($form->isSubmitted() && $form->isValid()) {
            $subscriptionValue = $form->get('subscription')->getData();

            // Vérifier si la date est déjà prise pour une salle
            $startDate = $form->get('startDate')->getData();
            $endDate = $form->get('endDate')->getData();

            $isDateAvailable = $this->isDateAvailableForWorkspace($workspaces, $startDate, $endDate, $entityManager);
            if (!$isDateAvailable) {
                $this->addFlash('danger', 'La date est déjà prise pour cette salle.');
                return $this->redirectToRoute('app_workspace_show', ['id' => $workspaces->getId()]);
            }
            
            if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
                throw new AccessDeniedException('Accès refusé. Vous devez être connecté pour effectuer cette action.');
            }

            $user = $this->security->getUser();
            if ($subscriptionValue !== null) {
                $user->setSubscription($subscriptionValue);
            }
            
            if ($workspaces->getCategoryWorkspace()->getTitle() === 'Salon privé') {
                $order->setNumberPassengers(null);
            }

            $order->setUser($user);
            $order->setWorkspace($workspaces);

            $entityManager->persist($order);
            $entityManager->persist($user);
            // dd($order);
            $entityManager->flush();
        }

        return $this->render('workspace/show.html.twig', [
            'workspace' => $workspaces,
            'form' => $form->createView(),
            'subscriptions' => $subscriptions,
        ]);
    }

    #[Route('/', name: 'app_workspace')]
    public function index(WorkspaceRepository $workspaceRepository): Response
    {
        $workspaces = $workspaceRepository->findAll();

        return $this->render('workspace/index.html.twig', [
            'workspaces' => $workspaces,
        ]);
    }

    private function isDateAvailableForWorkspace(Workspace $workspace, \DateTimeInterface $startDate, \DateTimeInterface $endDate, EntityManagerInterface $entityManager): bool
    {
        // Vérifier si la date est déjà prise pour la salle
        $query = $entityManager->createQueryBuilder()
            ->select('o')
            ->from(Order::class, 'o')
            ->where('o.workspace = :workspace')
            ->andWhere(':startDate < o.endDate')
            ->andWhere(':endDate > o.startDate')
            ->setParameter('workspace', $workspace)
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->getQuery();

        $existingOrders = $query->getResult();

        return count($existingOrders) === 0;
    }
}
