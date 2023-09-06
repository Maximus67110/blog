<?php

namespace App\Controller;

use App\Entity\Post;
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

    #[Route('/{slug}', name: 'app_post')]
    public function detail(Post $post): Response
    {
        return $this->render('home/post.html.twig', [
            'post' => $post,
        ]);
    }

    #[Route('/hello', name: 'app_hello')]
    public function hello(): Response
    {
        return $this->render('home/hello.html.twig');
    }
}
