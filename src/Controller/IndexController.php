<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Link;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\LocaleType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class IndexController extends AbstractController
{
    #[Route('/', name: 'app_index')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $categories = $entityManager->getRepository(Category::class)->findBy(
            ['parent' => null]
        );

        return $this->render('index/index.html.twig', [
            'categories' => $categories
        ]);
    }

    #[Route('/new', name: 'app_new')]
    public function new(EntityManagerInterface $entityManager): Response
    {
        $newLinks = $entityManager->getRepository(Link::class)->getAllNew();

        return $this->render('index/new.html.twig', [
            'links' => $newLinks,
        ]);
    }

    #[Route('/cool', name: 'app_cool')]
    public function cool(): Response
    {
        return $this->redirectToRoute('app_category', ['slug' => 'entertainment/cool-links']);
    }

    #[Route('/random', name: 'app_random')]
    public function random(EntityManagerInterface $entityManager): Response
    {
        $link = $entityManager->getRepository(Link::class)->getOneRandom();

        return new RedirectResponse($link->getURL());
    }

    #[Route('/info', name: 'app_info')]
    public function info(): Response
    {
        return $this->render('index/info.html.twig');
    }
}
