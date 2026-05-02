<?php

namespace App\Controller\Admin;

use App\Entity\Option;
use App\Form\OptionType;
use App\Repository\OptionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/options')]
class AdminOptionController extends AbstractController
{
    public function __construct(
        private OptionRepository $optionRepository,
        private EntityManagerInterface $em,
    ) {}

    #[Route('', name: 'admin_options')]
    public function index(): Response
    {
        return $this->render('admin/option/index.html.twig', [
            'options' => $this->optionRepository->findAll(),
        ]);
    }

    #[Route('/nouveau', name: 'admin_option_new')]
    public function new(Request $request): Response
    {
        $option = new Option();
        $form = $this->createForm(OptionType::class, $option);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($option);
            $this->em->flush();
            $this->addFlash('success', 'Option créée.');
            return $this->redirectToRoute('admin_options');
        }

        return $this->render('admin/option/form.html.twig', ['form' => $form, 'title' => 'Nouvelle option']);
    }

    #[Route('/{id}/modifier', name: 'admin_option_edit', requirements: ['id' => '\d+'])]
    public function edit(int $id, Request $request): Response
    {
        $option = $this->optionRepository->find($id);
        if (!$option) throw $this->createNotFoundException();

        $form = $this->createForm(OptionType::class, $option);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            $this->addFlash('success', 'Option modifiée.');
            return $this->redirectToRoute('admin_options');
        }

        return $this->render('admin/option/form.html.twig', ['form' => $form, 'title' => 'Modifier l\'option']);
    }

    #[Route('/{id}/supprimer', name: 'admin_option_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(int $id): Response
    {
        $option = $this->optionRepository->find($id);
        if ($option) {
            $this->em->remove($option);
            $this->em->flush();
            $this->addFlash('success', 'Option supprimée.');
        }

        return $this->redirectToRoute('admin_options');
    }
}
