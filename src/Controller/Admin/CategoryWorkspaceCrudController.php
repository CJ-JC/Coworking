<?php

namespace App\Controller\Admin;

use App\Entity\CategoryWorkspace;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class CategoryWorkspaceCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return CategoryWorkspace::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', 'Catégorie')
            ->setEntityLabelInPlural('CategoryWorkspace')
            ->setEntityLabelInSingular('CategoryWorkspace')
        ;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('title'),
        ];
    }
    
}
