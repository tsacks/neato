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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class GuideController extends AbstractController
{
    public EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/guide', name: 'app_guide')]
    public function index(): Response
    {
        $entityManager = $this->entityManager;
        $repository = $entityManager->getRepository(Link::class);
        $pendingLinks = $repository->findBy(
            ['approved' => false],
        );
        $allLinks = $repository->findAll();

        return $this->render('guide/index.html.twig', [
            'pendingLinks' => $pendingLinks,
            'allLinks' => $allLinks,
        ]);
    }

    #[Route('/guide/pending_links', name: 'app_guide_pending')]
    public function pending(): Response
    {
        $entityManager = $this->entityManager;
        $repository = $entityManager->getRepository(Link::class);
        $pendingLinks = $repository->findBy(
            ['approved' => false],
        );

        return $this->render('guide/pending.html.twig', [
            'links' => $pendingLinks,
        ]);
    }

    #[Route('/guide/links/approve/{id}', name: 'app_guide_link_approve')]
    public function approve_link(Link $link, EntityManagerInterface $entityManager): Response
    {
        $link->setApproved(true);
        $entityManager->persist($link);
        $entityManager->flush();

        return $this->redirectToRoute('app_guide_pending', [], 303);
    }

    #[Route('/guide/links/{id}', name: 'app_guide_link')]
    public function edit_link(Request $request, Link $link, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createFormBuilder($link)
            ->add('title' , TextType::class, [
            ])
            ->add('url', UrlType::class, [
            ])
            ->add('comment', TextareaType::class, [
            ])
            ->add('categories', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'title',
                'multiple' => true,
            ])
            ->add('city', TextType::class, [
                'required' => false,
            ])
            ->add('state', TextType::class, [
                'required' => false,
            ])
            ->add('country', CountryType::class, [
                'placeholder' => '',
                'required' => false,
            ])
            ->add('name', TextType::class, [
                'required' => false,
            ])
            ->add('email', EmailType::class, [
                'required' => false,
            ])
            ->add('contact', ChoiceType::class, [
                'choices'  => [
                    'Yes' => true,
                    'No' => false,
                ],
                'label' => "Can we contact you?",
                'required' => false,
                'placeholder' => false,
                'expanded' => true,
                'multiple' => false,
            ])
            ->add('submit', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // $location->getData() holds the submitted values
            // but, the original `$location` variable has also been updated
            $link = $form->getData();
            $entityManager->persist($link);
            $entityManager->flush();
            return $this->redirectToRoute('app_guide', [], 303);   
        }

        return $this->render('guide/link_edit.html.twig',[
            'form' => $form,
        ]);
    }

    #[Route('/guide/links', name: 'app_guide_links')]
    public function links(): Response
    {
        $entityManager = $this->entityManager;
        $repository = $entityManager->getRepository(Link::class);
        $links = $repository->findAll();

        return $this->render('guide/links.html.twig', [
            'links' => $links,
        ]);
    }
}
