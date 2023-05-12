<?php

namespace App\Controller;

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
        $user = $this->getUser();
        
        $form = $this->createForm(ContactType::class);

        if (!$user) {
            $form->remove('user');
        } else {
            $form->remove('email');
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contact = $form->getData();

            $contact->setCreatedAt(new \DateTime());

            $fromEmail = $user ? $user->getEmail() : $form->get('email')->getData();
            $contact->setEmail($fromEmail);

            $email = (new Email())
                ->from($fromEmail)
                ->to('cherley95@hotmail.fr')
                ->subject($contact->getSubject() ?? '')
                ->html($contact->getMessage());

            $entityManager->persist($contact);
            $entityManager->flush();

            $mailer->send($email);

            $this->addFlash('success', 'Votre message a été envoyé');

            return $this->redirectToRoute('app_contact');
        }

        return $this->render('contact/contact.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}
