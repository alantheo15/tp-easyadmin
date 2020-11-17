<?php

namespace App\Controller\Admin;

use App\Entity\Ecole\ArticleEcole;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use Vich\UploaderBundle\Form\Type\VichImageType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ArticleEcoleCrudController extends AbstractCrudController
{
	public static function getEntityFqcn(): string
	{
		return ArticleEcole::class;
	}

	public function configureFields(string $pageName): iterable
	{
		$imageView = ImageField::new( 'vignetteName' )->setBasePath( 'medias/images' )->setLabel( "Image" );
		$imageEdit = ImageField::new( 'vignetteFile' )->setFormType( VichImageType::class )->setFormTypeOptions(
			[
				'delete_label' => 'Supprimer l‘image',
				'download_uri' => false,
				'image_uri' => static function (ArticleEcole $ecole) {
					return $ecole->getWebPathImg();
				}
			]
		);

		switch ($pageName) {
			case Crud::PAGE_NEW:
			case Crud::PAGE_EDIT:
				return [
					TextField::new( "titre" ),
					AssociationField::new( 'classe', 'Classe' )->setFormTypeOption( "by_reference", false ),
					TextField::new( "description" ),
					$imageEdit
				];
			default:
				return [
					IdField::new( "titre" ),
					TextField::new( "description" ),
					TextField::new( "classe" ),
					$imageView
				];
		}
	}
}
