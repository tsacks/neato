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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AddController extends AbstractController
{
    #[Route('/add', name: 'app_add')]
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
        $link = new Link();

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
            return $this->redirectToRoute('app_index', [], 303);   
        }

        return $this->render('add/index.html.twig', [
            'form' => $form,
        ]);
    }
}
