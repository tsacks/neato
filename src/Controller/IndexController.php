<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Link;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\LocaleType;
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
            'controller_name' => 'IndexController',
            'categories' => $categories
        ]);
    }

    #[Route('/new', name: 'app_new')]
    public function new(): Response
    {
        return $this->render('index/new.html.twig', [
            'controller_name' => 'IndexController',
        ]);
    }

    #[Route('/cool', name: 'app_cool')]
    public function cool(): Response
    {
        return $this->render('index/cool.html.twig', [
            'controller_name' => 'IndexController',
        ]);
    }

    #[Route('/random', name: 'app_random')]
    public function random(EntityManagerInterface $entityManager): Response
    {
        $link = $entityManager->getRepository(Link::class)->getOneRandom();

        return new RedirectResponse($link->getURL());
    }

    #[Route('/headlines', name: 'app_headlines')]
    public function headlines(): Response
    {
        return $this->render('index/headlines.html.twig', [
            'controller_name' => 'IndexController',
        ]);
    }

    #[Route('/info', name: 'app_info')]
    public function info(): Response
    {
        return $this->render('index/info.html.twig', [
            'controller_name' => 'IndexController',
        ]);
    }

    #[Route('/add', name: 'app_add')]
    public function add(): Response
    {
        $link = new Link();

        $form = $this->createFormBuilder($link)
            ->add('title' , TextType::class, [
                'label_attr' => ['class' => 'label'],
            ])
            ->add('url', UrlType::class, [
                'label_attr' => ['class' => 'label'],
            ])
            ->add('comment', TextareaType::class, [
                'label_attr' => ['class' => 'label'],
            ])
            ->add('city', TextType::class, [
                'label_attr' => ['class' => 'label'],
            ])
            ->add('state', TextType::class, [
                'label_attr' => ['class' => 'label'],
            ])
            ->add('country', CountryType::class, [
                'placeholder' => '',
                'label_attr' => ['class' => 'label'],
            ])
            ->add('name', TextType::class, [
                'label_attr' => ['class' => 'label'],
            ])
            ->add('email', EmailType::class, [
                'label_attr' => ['class' => 'label'],
            ])
            ->add('contact', ChoiceType::class, [
                'label_attr' => ['class' => 'label'],
                'choices'  => [
                    'Yes' => true,
                    'No' => false,
                ],
                'expanded' => true,
                'multiple' => false,
            ])
            ->getForm();

        return $this->render('index/add.html.twig', [
            'form' => $form,
        ]);
    }
}
