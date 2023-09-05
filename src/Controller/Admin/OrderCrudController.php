<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;

class OrderCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Order::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', 'Les réservations')
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable('create')
            ->disable('edit');
    }
    
    public function configureFields(string $pageName): iterable
    {
        return [
            AssociationField::new('user', 'Client')->formatValue(function ($value, $entity) {
                return $entity->getUser()->getFirstname()." ".$entity->getUser()->getLastname();
            }),            
            DateField::new('start_date', 'Date de début'),
            DateField::new('end_date', "Date de fin"),
            NumberField::new('price', "Prix"),
            TextField::new('reference', "Réference"),
            NumberField::new('number_passengers', "Nombre de personne"),
            DateField::new('created_at', "Date de réservation"),
            AssociationField::new('subscription', "Forfait"),
            AssociationField::new('workspace', "Salon"),
        ];
    }
}
