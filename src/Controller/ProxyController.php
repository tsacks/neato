<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProxyController extends AbstractController
{
    #[Route('/proxy', name: 'app_proxy')]
    public function index(): Response
    {
        return $this->render('proxy/index.html.twig', [
            'controller_name' => 'ProxyController',
        ]);
    }

    #[Route('/proxy/image', name: 'app_proxy_image')]
    public function image(Request $request): Response
    {
        $decoded_url = urldecode($request->query->get('url'));
        $client = HttpClient::create();
        $file = $client->request('GET', $decoded_url);
        $fileContent = $file->getContent();

        $response = new Response($fileContent);
        $response->headers->set('Content-Type', $file->getInfo('content_type')); 
        return $response;
    }
}
