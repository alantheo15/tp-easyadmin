<?php

namespace App\Controller\Admin;

use App\Entity\Ecole\Classe;
use App\Entity\Ecole\Enseignant;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Vich\UploaderBundle\Form\Type\VichImageType;

class EnseignantCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Enseignant::class;
    }


    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle(Crud::PAGE_INDEX, "Enseignant")
            ->setPageTitle(Crud::PAGE_EDIT, "Edition de l'enseignant");
    }

	/*
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setFormThemes(
                [
                    '@EasyAdmin/crud/form_theme.html.twig',
                ]
            )
            ->overrideTemplate( "crud/edit", "admin/edit/Enseignant_EditDashboard.html.twig" )
            ->overrideTemplate( "crud/new", "admin/new/Enseignant_NewDashboard.html.twig" );
    } */

    public function configureFields(string $pageName): iterable
    {
        $imageView = ImageField::new( 'vignetteName' )->setBasePath( 'medias/images' )->setLabel( "Image" );
        $imageEdit = ImageField::new( 'vignetteFile' )->setFormType( VichImageType::class )->setFormTypeOptions(
            [
                'delete_label' => 'Supprimer l‘image',
                'download_uri' => false,
                'image_uri' => static function (Enseignant $enseignant) {
                    return $enseignant->getWebPathImg();
                }
            ]
        );

        switch ($pageName) {
            case Crud::PAGE_NEW:
            case Crud::PAGE_EDIT:
                return [
                    TextField::new( 'identite' ),
                    BooleanField::new( "visible" ),
                    AssociationField::new( 'classes', 'Classes' )->setFormTypeOption( "by_reference", false ),
                    AssociationField::new( 'classes', 'Classes' )->setFormTypeOption( "by_reference", false ),
                    DateField::new( 'createdAt' )->setLabel( "Date de création" )->setFormTypeOptions( ["attr" => ["class" => "form-control"]] ),
                    $imageEdit
                ];
            default:
                return [
                    IdField::new( "id" ),
                    TextField::new( "identite" ),
                    $imageView
                ];
        }
    }

}
