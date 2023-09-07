<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\Tag;
use App\Entity\User;
use App\Form\CommentType;
use App\Repository\PostRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(Request $request, PostRepository $postRepository): Response
    {
        if (($value = $request->query->get('value'))) {
            $posts = $postRepository->search($value);
        } else {
            $posts = $postRepository->findAll();
        }
        return $this->render('home/home.html.twig', [
            'posts' => $posts,
        ]);
    }

    #[Route('/tag/{id}', name: 'app_tag')]
    public function postByTag(Tag $tag): Response
    {
        return $this->render('home/tag.html.twig', [
            'posts' => $tag->getPosts(),
        ]);
    }

    #[Route('/user/{id}', name: 'app_user')]
    public function postByUser(User $user, PostRepository $postRepository): Response
    {
        $posts = $postRepository->findBy(['user'=> $user], ['createdAt' => Criteria::DESC]);
        return $this->render('home/user.html.twig', [
            'posts' => $posts,
        ]);
    }

    #[Route('/{slug}', name: 'app_post')]
    public function detail(Request $request, Post $post, EntityManagerInterface $entityManager): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setUser($this->getUser());
            $comment->setPost($post);
            $entityManager->persist($comment);
            $entityManager->flush();
            return $this->redirectToRoute('app_post', ['slug' => $post->getSlug()]);
        }
        return $this->render('home/post.html.twig', [
            'post' => $post,
            'form' => $form,
        ]);
    }

    #[Route('/comment/delete/{id}', name: 'app_comment_delete')]
    public function deleteComment(Comment $comment, EntityManagerInterface $entityManager): Response
    {
        $post = $comment->getPost();
        if ($this->getUser()?->getId() !== $comment->getUser()?->getId()) {
            return $this->redirectToRoute('app_post', ['slug' => $post?->getSlug()]);
        }
        $entityManager->remove($comment);
        $entityManager->flush();
        return $this->redirectToRoute('app_post', ['slug' => $post?->getSlug()]);
    }
}
