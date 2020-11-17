<?php

namespace App\Controller\Admin;

use App\Entity\Ecole;
use App\Entity\Ecole\Classe;
use App\Entity\Ecole\Enseignant;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\CrudUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        //return parent::index();
        $routeBuilder = $this->get( CrudUrlGenerator::class )->build();
        return $this->redirect( $routeBuilder->setController( EnseignantCrudController::class )->generateUrl() );
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('TP Sur EasyAdmin');
    }

    public function configureMenuItems(): iterable
    {
        [
            yield MenuItem::linktoDashboard('Dashboard', 'fa fa-home'),
            yield MenuItem::section( "Les Écoles", "fa fa-book-open" ),
            yield MenuItem::linkToCrud('Users', 'fa fa-tags', User::class),
            yield MenuItem::linkToCrud('Ecole', 'fa fa-tags', Ecole::class),
            yield MenuItem::linkToCrud('Articles école', 'fa fa-tags', Ecole\ArticleEcole::class),
            yield MenuItem::linkToCrud('Classes', 'fa fa-tags', Classe::class),
            yield MenuItem::linkToCrud('Enseignants', 'fa fa-tags', Enseignant::class)
        ];

        // ajouter restriction par la suite ici pour pas que n'importe qui puisse allez modifier l'importation
        [
            yield MenuItem::section( "Importation École", "fa fa-book" ),
            yield MenuItem::linktoRoute( "Importation d'écoles", "fa fa-download", "uploadEcole" )

        ];

        // yield MenuItem::linkToCrud('The Label', 'icon class', EntityClass::class);
    }
}
