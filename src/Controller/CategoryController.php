<?php

namespace App\Controller;

use App\Entity\CategoryWorkspace;
use App\Form\CategoryType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    #[Route('/category', name: 'app_category')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $category = new CategoryWorkspace();

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            // $workspace = $form->getData();
            
            $entityManager->persist($category);
            $entityManager->flush();
        }

        return $this->render('category/index.html.twig', [
            'form' => $form->createView(),
            'category' => $category,
        ]);
    }
}
