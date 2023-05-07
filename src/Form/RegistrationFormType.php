<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', TextType::class, [
                'label' => 'Email',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Votre email',
                    // 'class' => 'form-control',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'L\'email est obligatoire',
                    ])
                ]
            ])
            ->add('plainPassword', PasswordType::class, [
                'label' => 'Mot de passe',
                'required' => true,
                'mapped' => false,
                'attr' => [
                        'autocomplete' => 'new-password',
                        'placeholder' => 'Votre mot de passe',
                        // 'class' => 'form-control',
                    ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le mot de passe est obligatoire',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit comporter au moins {{ limit }} caractères',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
