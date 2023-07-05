<?php

namespace App\Controller;

use Dompdf\Dompdf;
use Stripe\Stripe;
use App\Entity\User;
use App\Entity\Order;
use App\Form\OrderType;
use App\Entity\Workspace;
use Symfony\Component\Mime\Email;
use App\Repository\WorkspaceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mime\Part\DataPart;
use App\Repository\SubscriptionRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mime\Part\Multipart\MixedPart;
use Symfony\Component\Mime\Part\Multipart\AlternativePart;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[Route('/workspace')]
class WorkspaceController extends AbstractController
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    #[Route('/{id}', name: 'app_workspace_show')]
    public function show(string $id, Workspace $workspace, Request $request, EntityManagerInterface $entityManager, SubscriptionRepository $subscription): Response
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
            if ($workspace->getCategoryWorkspace()->getTitle() !== 'Salon principal') {
                // Vérifier les contraintes d'heure uniquement si ce n'est pas un "Salon principal"
                $isDateAvailable = $this->isDateAvailableForWorkspace($workspace, $startDate, $endDate, $entityManager);
                if (!$isDateAvailable) {
                    $this->addFlash('danger', 'La date est déjà prise pour cette salle.');
                    return $this->redirectToRoute('app_workspace_show', ['id' => $workspace->getId()]);
                }
            }
            
            if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
                throw new AccessDeniedException('Accès refusé. Vous devez être connecté pour effectuer cette action.');
            }

            /** @var User $user */
            $user = $this->security->getUser();
            if ($subscriptionValue !== null) {
                $user->setSubscription($subscriptionValue);
            }
            
            if ($workspace->getCategoryWorkspace()->getTitle() === 'Salon privé') {
                $order->setNumberPassengers(null);
            }

            // Créez une nouvelle commande
            $order->setUser($user);
            $order->setWorkspace($workspace);
            $order->setCreatedAt(new \DateTimeImmutable());
            $order->setReference(uniqid('', false));

            if ($subscriptionValue !== null) {
                $order->setPrice($workspace->getPrice() + $subscriptionValue->getPrice());
            } else {
                $order->setPrice($workspace->getPrice());
            }

            $numberOfPassengers = $form->get('numberPassengers')->getData();
            $remainingPlaces = $workspace->getRemainingPlaces();

            if ($numberOfPassengers > $remainingPlaces) {
                $this->addFlash('danger', 'Désolé, il n\'y a pas suffisamment de places disponibles pour votre réservation.');
                return $this->redirectToRoute('app_workspace_show', ['id' => $workspace->getId()]);
            }

            // Stocker la clé Stripe dans une variable
            $stripeApiKey = $_ENV['STRIPE_SECRET_KEY_TEST'];

            // Utiliser la clé Stripe
            Stripe::setApiKey($stripeApiKey);

            $entityManager->persist($order);
            $entityManager->flush();

            $unitAmount = $order->getPrice() * 100;

            $checkoutSession = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => [
                            'name' => $workspace->getCategoryWorkspace()->getTitle(),
                        ],
                        'unit_amount' => $unitAmount,
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => $this->generateUrl('app_payment_success', ['id' => $order->getId()], UrlGeneratorInterface::ABSOLUTE_URL),
                'cancel_url' => $this->generateUrl('app_payment_cancel', ['id' => $order->getId()], UrlGeneratorInterface::ABSOLUTE_URL),
            ]);

            $entityManager->flush();

            // Redirigez l'utilisateur vers l'URL de paiement Stripe
            return $this->redirect($checkoutSession->url);
        }

        return $this->render('workspace/show.html.twig', [
            'form' => $form->createView(),
            'workspace' => $workspace,
            'subscriptions' => $subscriptions,
        ]);
    }

    #[Route("/payment/success/{id}", name: "app_payment_success")]
    public function paymentSuccess(Order $order, MailerInterface $mailer, SessionInterface $session): Response
    {
            $workspace = $order->getWorkspace();
            
            /** @var User $user */
            $user = $this->getUser();
        // Vérifier si le mail a déjà été envoyé
        if (!$session->get('payment_email_sent')) {
        
            // Rendre la vue Twig et obtenir son contenu HTML
            $imagePath = 'img/icons/logo.png';
            $htmlContent = $this->renderView('email/reservation.html.twig', [
                'order' => $order,
                'logoUrl' => $imagePath,
            ]);

            // Utiliser Dompdf pour générer le fichier PDF
            $dompdf = new Dompdf();
            $dompdf->loadHtml($htmlContent);
            $dompdf->render();
            $pdfContent = $dompdf->output();

            // Créer l'objet Email avec le contenu HTML et l'attachement
            $email = (new Email())
                ->from($user->getEmail())
                ->to('contact@gusto.fr')
                ->subject('Objet : Confirmation de réservation d\'espace de travail')
                ->html($htmlContent);

            $email->attach($pdfContent, 'reservation.pdf', 'application/pdf');
            $mailer->send($email);
            
            // Marquer l'envoi du mail pour éviter les envois multiples
            $session->set('payment_email_sent', true);
        }

        // Affichez les détails de la commande et de l'espace de travail dans le template Twig
        return $this->render('workspace/payment_success.html.twig', [
            'order' => $order,
            'workspace' => $workspace,
        ]);
    }

    #[Route("/payment/cancel/{id}", name: "app_payment_cancel")]
    public function paymentCancel(Order $order, EntityManagerInterface $entityManager): Response
    {
        // Récupérez l'utilisateur associé à la commande
        $user = $order->getUser();

        // Supprimez la réservation
        $entityManager->remove($order);

        // Supprimez le forfait de l'utilisateur
        if ($user !== null) {
            $user->setSubscription(null);
        }

        //Enregistrez les modifications dans la base de données
        $entityManager->flush();

        $this->addFlash('danger', 'Le paiement de la réservation a échoué');

        $workspace = $order->getWorkspace();
        return $this->redirectToRoute('app_workspace_show', ['id' => $workspace->getId()]);
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
