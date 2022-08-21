<?php

namespace App\Controller\Admin;

use App\Entity\Spec;
use App\Form\SpecType;
use App\Repository\SpecRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/spec')]
class AdminSpecController extends AbstractController
{
    #[Route('/', name: 'admin.spec.index', methods: ['GET'])]
    public function index(SpecRepository $specRepository): Response
    {
        return $this->render('admin/spec/index.html.twig', [
            'specs' => $specRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'admin.spec.new', methods: ['GET', 'POST'])]
    public function new(Request $request, SpecRepository $specRepository): Response
    {
        $spec = new Spec();
        $form = $this->createForm(SpecType::class, $spec);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $specRepository->add($spec, true);

            return $this->redirectToRoute('admin.spec.index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/spec/new.html.twig', [
            'spec' => $spec,
            'form' => $form,
        ]);
    }

    // #[Route('/{id}', name: 'admin.spec.show', methods: ['GET'])]
    // public function show(Spec $spec): Response
    // {
    //     return $this->render('admin/spec/show.html.twig', [
    //         'spec' => $spec,
    //     ]);
    // }

    #[Route('/{id}/edit', name: 'admin.spec.edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Spec $spec, SpecRepository $specRepository): Response
    {
        $form = $this->createForm(SpecType::class, $spec);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $specRepository->add($spec, true);

            return $this->redirectToRoute('admin.spec.index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/spec/edit.html.twig', [
            'spec' => $spec,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin.spec.delete', methods: ['POST'])]
    public function delete(Request $request, Spec $spec, SpecRepository $specRepository): Response
    {
        if ($this->isCsrfTokenValid('admin/delete' . $spec->getId(), $request->request->get('_token'))) {
            $specRepository->remove($spec, true);
        }

        return $this->redirectToRoute('admin.spec.index', [], Response::HTTP_SEE_OTHER);
    }
}
