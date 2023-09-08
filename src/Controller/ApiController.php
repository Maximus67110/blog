<?php

namespace App\Controller;

use App\Repository\PostRepository;
use Doctrine\Common\Collections\Criteria;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class ApiController extends AbstractController
{
    #[Route('/post', name: 'app_api_post')]
    public function index(Request $request, PostRepository $postRepository): Response
    {
        $limit = $request->query->get('limit', 4);
        $offset = $request->query->get('offset');
        $posts = $postRepository->findBy([], ['createdAt' => Criteria::DESC], $limit, $offset);
        return $this->json($posts, 200, [], ['groups' => ['post_list']]);
    }
}
