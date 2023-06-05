<?php

namespace App\Form;

use App\Entity\CategoryWorkspace;
use App\Entity\ImageSave;
use App\Entity\Order;
use App\Entity\Workspace;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class WorkspaceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nbrPlace', TextType::class,[
                'label' => 'Nombre de place',
            ])
            ->add('title', TextType::class)
            ->add('price', MoneyType::class)
            ->add('description', TextareaType::class)
            ->add('imageFile', EntityType::class,[
                'class' => ImageSave::class,
                'choice_label' => 'imageFile',
                'label' => 'Image de la salle'
            ])
            ->add('categoryWorkspace', EntityType::class,[
                'class' => CategoryWorkspace::class,
                'choice_label' => 'title'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Workspace::class,
        ]);
    }
}
