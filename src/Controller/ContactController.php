<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function contactForm(Request $request, MailerInterface $mailer, EntityManagerInterface $entityManager): Response
    {
        $contact = new Contact();

        $form = $this->createForm(ContactType::class, $contact);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var User $user */
            $user = $this->getUser();

            $contact = $form->getData();
            $contact->setCreatedAt(new \DateTime());

            $entityManager->persist($contact);
            $entityManager->flush();

            $htmlContent = $this->renderView('email/contact.html.twig',[
                'contact' => $contact,
            ]);
    
            $email = (new Email())
                // ->from($user->getEmail())
                ->to('gusto-coffee@f2i-dev22-23-de-cj-lt-db.fr')
                ->subject($contact->getSubject() ?? '')
                ->html($htmlContent);

            if ($user) {
                $email->from($user->getEmail());
            } else {
                $email->from($contact->getEmail());
            }

            $mailer->send($email);

            $this->addFlash('success', 'Votre message a été envoyé');

            return $this->redirectToRoute('app_contact');
        }

       return $this->render('contact/contact.html.twig', [
            'form' => $form->createView(),
       ]);
    }
}
