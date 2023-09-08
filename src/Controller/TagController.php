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
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("ROLE_ADMIN")]
#[Route('/admin/tag')]
class TagController extends AbstractController
{
    #[Route('/list', name: 'app_tag_list')]
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

    #[Route('/edit/{id}', name: 'app_tag_edit', requirements: ['id' => '\d+'])]
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

    #[Route('/{id}', name: 'app_tag_detail', requirements: ['id' => '\d+'])]
    public function detail(Tag $tag): Response
    {
        return $this->render('tag/detail.html.twig', [
            'tag' => $tag,
        ]);
    }

    #[Route('/delete/{id}', name: 'app_tag_delete', requirements: ['id' => '\d+'])]
    public function delete(Tag $tag, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($tag);
        $entityManager->flush();
        return $this->redirectToRoute('app_tag_list');
    }
}
