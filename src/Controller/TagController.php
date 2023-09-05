<?php

namespace App\Controller;

use App\Entity\Tag;
use App\Form\TagType;
use App\Repository\TagRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/tag')]
class TagController extends AbstractController
{
    #[Route('', name: 'app_tag_list')]
    public function list(TagRepository $tagRepository): Response
    {
        $tags = $tagRepository->findAll();
        return $this->render('tag/list.html.twig', [
            'tags' => $tags,
        ]);
    }

    #[Route('/create', name: 'app_tag_create')]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $tag = new Tag();
        $form = $this->createForm(TagType::class, $tag);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($tag);
            $entityManager->flush();
            return $this->redirectToRoute('app_tag_list');
        }
        return $this->render('tag/create.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/edit/{id}', name: 'app_tag_edit')]
    public function edit(Request $request, Tag $tag, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TagType::class, $tag);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($tag);
            $entityManager->flush();
            return $this->redirectToRoute('app_tag_list');
        }
        return $this->render('tag/edit.html.twig', [
            'form' => $form,
        ]);
    }
}
