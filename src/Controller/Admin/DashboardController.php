<?php

namespace App\Controller\Admin;

use App\Entity\CategoryWorkspace;
use App\Entity\Subscription;
use App\Entity\User;
use App\Entity\Workspace;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig');

        // Option 1. You can make your dashboard redirect to some common page of your backend
        // return $this->redirect($adminUrlGenerator->setController(OneOfYourCrudController::class)->generateUrl());
        //
        // $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        // return $this->render('some/path/my-dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('GUSTO COFFEE');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Liste d\'utilisateurs', 'fa fa-user', User::class);
        yield MenuItem::linkToCrud('Les espaces', 'fa fa-list', Workspace::class);
        yield MenuItem::linkToCrud('Les forfaits', 'fa fa-subscript', Subscription::class);
        yield MenuItem::linkToCrud('Les catégories', 'fa fa-caret-square-o-right', CategoryWorkspace::class);
        yield MenuItem::linkToRoute('Télécharger la base de données', 'fa fa-download', 'backup_download')
            ->setLinkTarget('_blank'); // Pour ouvrir dans un nouvel onglet
    }
}
