<?php

 

namespace App\Controller;


use App\Repository\WorkspaceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Entity\Workspace;
use Doctrine\ORM\EntityManagerInterface;

class ApiWorkspaceController extends AbstractController

{
    #[Route('/api/workspace', name: 'app_api_index', methods: ['GET'])]
    public function index(WorkspaceRepository $workspaceRepository)
    {
        return $this->json($workspaceRepository->findAll(), 200, [], ['groups' => 'workspace:read']);
    }
 

    #[Route('/api/workspace', name: 'app_api_store', methods: ['POST'])]
    public function store(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, ValidatorInterface $validator)
    {
        $jsonRecu = $request->getContent();
        try {
            $workspace = $serializer->deserialize($jsonRecu, Workspace::class, 'json');
            $errors = $validator->validate($workspace);
            if (count($errors) > 0) {

                // Si des erreurs de validation sont trouvées, retourner un message d'erreur
                $errorMessages = [];
                foreach ($errors as $error) {
                    $errorMessages[] = $error->getMessage();
                }

                return $this->json([
                    'status' => 400,
                    'message' => 'Validation failed',
                    'errors' => $errorMessages,
                ], 400);
            }

            $em->persist($workspace);
            $em->flush();

            return $this->json($workspace, 201, [], ['groups' => 'workspace:read']);

        } catch (NotEncodableValueException $e) {
            return $this->json([
                'status' => 400,
                'message' => $e->getMessage(),

            ], 400);
        }
    }
}