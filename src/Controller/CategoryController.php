<?php

namespace App\Controller;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CategoryController extends AbstractController
{
    #[Route('/cat/{slug:category}', name: 'app_category', requirements: ['slug' => '.+'])]
    public function index(EntityManagerInterface $entityManager, ?Category $category): Response
    {
        return $this->render('category/index.html.twig', [
            'controller_name' => 'IndexController',
            'category' => $category
        ]);
    }

    #[Route('/cat', name: 'app_category_default')]
    public function default(): Response
    {
        return $this->redirectToRoute('app_index');
    }
}
