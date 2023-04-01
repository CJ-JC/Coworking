<?php

namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fullName', TextType::class,[
                'attr' => [
                    'placeholder' => 'Votre nom complet',
                    'class' => 'form-control',
                ]
            ])
            ->add('email', TextType::class,[
                'attr' => [
                    'placeholder' => 'Votre email',
                    'class' => 'form-control',
                ]
            ])
            ->add('subject', TextType::class,[
                'attr' => [
                    'placeholder' => 'Le sujet de votre demande',
                    'class' => 'form-control',
                ]
            ])
            ->add('message', TextareaType::class,[
                'attr' => [
                    'placeholder' => 'Votre message',
                    'class' => 'form-control'
                ]
            ])
            ->add('Envoyer', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
