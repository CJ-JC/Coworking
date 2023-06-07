<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Order;
use App\Form\OrderType;
use Stripe\StripeClient;
use App\Entity\Workspace;
use App\Entity\Subscription;
use App\Service\CountPlaceService;
use App\Repository\WorkspaceRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\SubscriptionRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[Route('/workspace')]
class WorkspaceController extends AbstractController
{
    private $security;
    private $manager;
    private $gateway;

    public function __construct(Security $security, EntityManagerInterface $manager)
    {
        $this->security = $security;
        $this->manager = $manager;
        $this->gateway = new StripeClient($_ENV['STRIPE_SECRET_KEY_TEST']);
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

            // Vérifier si la salle est de catégorie "Salon principal"
            if ($workspaces->getCategoryWorkspace()->getTitle() !== 'Salon principal') {
                // Vérifier les contraintes d'heure uniquement si ce n'est pas un "Salon principal"
                $isDateAvailable = $this->isDateAvailableForWorkspace($workspaces, $startDate, $endDate, $entityManager);
                if (!$isDateAvailable) {
                    $this->addFlash('danger', 'La date est déjà prise pour cette salle.');
                    return $this->redirectToRoute('app_workspace_show', ['id' => $workspaces->getId()]);
                }
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

            // Vérifier si la date de fin de réservation est passée
            $currentDate = new \DateTime();
            if ($endDate < $currentDate) {
                // Supprimer le client associé
                $order->setUser(null);
            }

        //     if($request->getMethod() === "POST") {
        //         $formData = $request->request->all();
                
        //         $order->setUser($user);
        //         $order->setWorkspace($workspaces);

        //         $amount = $order->getWorkspace()->getPrice();

        //         $checkout=$this->gateway->checkout->sessions->create(
        //         [
        //                 'line_items'=>[[
        //                     'price_data'=>[
        //                     'currency'=> "EUR",
        //                     'product_data'=>[
        //                         'name'=> $workspaces->getTitle(),
        //                     ],
        //                     'unit_amount'=>intval($amount) * 100,
        //                 ],
        //                 'quantity'=> 1,
        //                 ]],
    
        //                 'mode'=>'payment',
        //                 'success_url'=>'https://127.0.0.1:8001/success?id_sessions={CHECKOUT_SESSION_ID}',
        //                 'cancel_url'=>'https://127.0.0.1:8001/cancel?id_sessions={CHECKOUT_SESSION_ID}'
        //         ]);
    
        //         // dd($checkout);
        //         return $this->redirect($checkout->url);

        
        //             return $this->render('workspace/paiement.html.twig', [
            //                 'workspace' => $workspaces,
            //                 'formData' => $formData,
            //             ]);
            //         }
            //         // dd($order);

            $entityManager->flush();
            $entityManager->persist($order);
            $entityManager->persist($user);
        }

        return $this->render('workspace/show.html.twig', [
            'workspace' => $workspaces,
            'form' => $form->createView(),
            'subscriptions' => $subscriptions,
        ]);
    }

    #[Route('/success', name: 'app_success')]
    public function success(Request $request): Response
    {
        $id_sessions=$request->query->get('id_sessions');

        
        //Récupère le customer via l'id de la  session
        $customer=$this->gateway->checkout->sessions->retrieve(
            $id_sessions,
            []
        );

        //Récupérer les informations du customer et de la transaction

        $name= $customer["customer_details"]["name"];

        $email= $customer["customer_details"]["email"];

        $payment_status = $customer["payment_status"];

        $amount = $customer['amount_total'];

        //Stocker au niveau de la base de données



        //Email au customer




        //Message de succès


        return $this->render('success/success.html.twig',[
            'name'=> $name,
            'amount'=> $amount,
            'email'=> $email,
            'payement'=> $payment_status,
        ]);

    }


    #[Route('/cancel', name: 'app_cancel')]
    public function cancel(Request $request): Response
    {
        dd("cancel");
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
