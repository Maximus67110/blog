<?php

namespace App\Controller;

use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(PostRepository $postRepository): Response
    {
        $posts = $postRepository->findAll();
        return $this->render('home/home.html.twig', [
            'posts' => $posts,
        ]);
    }

    #[Route('/hello', name: 'app_hello')]
    public function hello(): Response
    {
        return $this->render('home/hello.html.twig');
    }
}
