<?php

namespace App\Form;

use App\Entity\Contact;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('user', EntityType::class, [
                'class' => User::class,
                'disabled' => true, 
                'label' => 'Email',
                'choice_label' => 'email'
            ])
            ->add('fullName', TextType::class,[
                'required' => false,
                'label' => 'Nom complet*',
                'attr' => [
                    'placeholder' => 'Votre nom',
                    'class' => 'form-control',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le nom est obligatoire',
                    ])
                ]
            ])
            ->add('email', EmailType::class,[
                'required' => false,
                'label' => 'Email*',
                'attr' => [
                    'placeholder' => 'Votre email',
                    'class' => 'form-control',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'L\'email est obligatoire',
                    ])
                ]
            ])
            ->add('subject', TextType::class,[
                'required' => false,
                'label' => 'Objet',
                'attr' => [
                    'placeholder' => 'L\'objet',
                    'class' => 'form-control',
                ],
            ])
            ->add('message', TextareaType::class,[
                'required' => false,
                'label' => 'Message*',
                'attr' => [
                    'placeholder' => 'Votre message',
                    'class' => 'form-control',
                    'cols' => '30',
                    'rows' => '5'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le message est obligatoire',
                    ])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
