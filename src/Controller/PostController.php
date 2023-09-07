<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

#[IsGranted("ROLE_ADMIN")]
#[Route('/admin/post')]
class PostController extends AbstractController
{
    #[Route('/list', name: 'app_post_list')]
    public function list(PostRepository $postRepository): Response
    {
        $posts = $postRepository->findAll();
        return $this->render('post/list.html.twig', [
            'posts' => $posts,
        ]);
    }

    #[Route('/create', name: 'app_post_create')]
    public function create(Request $request, EntityManagerInterface $entityManager, FileUploader $fileUploader, SluggerInterface $slugger): Response
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $imageFileName = $fileUploader->upload($imageFile);
                $post->setImage($imageFileName);
            }
            /** @var string $postTitle */
            $postTitle = $form->get('title')->getData();
            if ($postTitle) {
                $post->setSlug((string) $slugger->slug($postTitle));
            }
            $post->setUser($this->getUser());
            $entityManager->persist($post);
            $entityManager->flush();
            return $this->redirectToRoute('app_post_list');
        }
        return $this->render('post/create.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/edit/{id}', name: 'app_post_edit')]
    #[IsGranted('edit', 'post')]
    public function edit(Request $request, Post $post, EntityManagerInterface $entityManager, FileUploader $fileUploader, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $imageFileName = $fileUploader->upload($imageFile);
                $post->setImage($imageFileName);
            }
            /** @var string $postTitle */
            $postTitle = $form->get('title')->getData();
            if ($postTitle) {
                $post->setSlug((string) $slugger->slug($postTitle));
            }
            $entityManager->persist($post);
            $entityManager->flush();
            return $this->redirectToRoute('app_post_list');
        }
        return $this->render('post/edit.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_post_detail')]
    #[IsGranted('show', 'post')]
    public function show(Post $post): Response
    {
        return $this->render('post/detail.html.twig', [
            'post' => $post,
        ]);
    }

    #[Route('/delete/{id}', name: 'app_post_delete')]
    #[IsGranted('delete', 'post')]
    public function delete(Post $post, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($post);
        $entityManager->flush();
        return $this->redirectToRoute('app_post_list');
    }
}
