<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', 'Nos utilisateurs')
            ->setEntityLabelInPlural('Utilisateur')
            ->setEntityLabelInSingular('Utilisateur')
            ->setPaginatorPageSize(10)
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable('edit');
    }
    
    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('firstname', "Nom"),
            TextField::new('lastname', "Prénom"),
            TextField::new('email', "Email"),
        ];
    }
}
