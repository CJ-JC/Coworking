<?php

namespace App\Controller\Admin;

use App\Entity\Workspace;
use App\Entity\ImageSave;
use App\Form\ImageSaveType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;

class WorkspaceCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Workspace::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', 'Nos espaces de travail')
            ->setEntityLabelInPlural('Espace de travail')
            ->setEntityLabelInSingular('Espace de travail')
            ->setPaginatorPageSize(10)
        ;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            NumberField::new('nbrPlace'),
            NumberField::new('price'),
            // MoneyField::new('price'),
            TextEditorField::new('description'),
            AssociationField::new('categoryWorkspace'),
            CollectionField::new('imageSaves')
                ->setEntryType(ImageSaveType::class)
        ];
    }
    
}
