<?php

namespace App\Form;

use App\Entity\Order;
use App\Entity\Subscription;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // ->add('title')
            // ->add('price')
            // ->add('description')
            // ->add('orderHour')
            ->add('numberPassengers', IntegerType::class, [
                'label' => 'Nombre de personnes',
                'data' => 1,
                'attr' => [
                    'readonly' => true,
                    'class' => 'input-number',
                    'max' => 10
                ]
            ])
            ->add('startDate', DateType::class, [
                'label' => 'Début de réservation',
                'widget' => 'single_text',
                'data' => (new \DateTime())->setTime(0, 0, 0),
                'constraints' => [
                    new NotBlank(),
                    new GreaterThanOrEqual(['value' => (new \DateTime())->setTime(0, 0, 0)])
                ]
            ])
            ->add('endDate', DateType::class, [
                'label' => 'Fin de réservation',
                'widget' => 'single_text',
                'data' => (new \DateTime())->setTime(0, 0, 0), 
                'data' => new \DateTime('last day of this month'),
                'constraints' => [
                    new NotBlank(),
                    new GreaterThanOrEqual(['value' => (new \DateTime())->setTime(0, 0, 0)])
                ]
            ])
            ->add('subscription', EntityType::class, [
                'class' => Subscription::class,
                'label' => false,
                'choice_label' => 'title',
                'expanded' => true,
                'required' => false,
                'placeholder' => 'Aucun Abonnement',
                'choice_attr' => function($choice, $key, $value) {
                    if ($key === 'first') {
                        return ['data-price' => 0];
                    } elseif (empty($choice->getPrice())) {
                        return ['data-price' => 0];
                    } else {
                        return [
                            'data-price' => $choice->getPrice(),
                            'data-id' => $choice->getId(),
                        ];
                    }
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
        ]);
    }
}
