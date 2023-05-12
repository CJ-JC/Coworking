<?php

namespace App\Controller;

use App\Entity\Workspace;
use App\Repository\WorkspaceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WorkspaceController extends AbstractController
{
    #[Route('/workspace', name: 'app_workspace')]
    public function index(WorkspaceRepository $workspace): Response
    {
        $workspaces = $workspace->findAll();

        return $this->render('workspace/index.html.twig', [
            'workspaces' => $workspaces,
        ]);
    }

    #[Route('/workspace/{id}', name: 'app_workspace_show')]
    public function show(Workspace $workspaces): Response
    {
        return $this->render('workspace/show.html.twig', [
            'workspace' => $workspaces,
        ]);
    }
}
