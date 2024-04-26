<?php

namespace App\Controller;

use App\Entity\Especialidad;
use App\Form\EspecialidadType;
use App\Repository\EspecialidadRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/especialidad')]
class EspecialidadController extends AbstractController
{
    #[Route('/', name: 'app_especialidad_index', methods: ['GET'])]
    public function index(EspecialidadRepository $especialidadRepository): Response
    {
        return $this->render('especialidad/index.html.twig', [
            'especialidads' => $especialidadRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_especialidad_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $especialidad = new Especialidad();
        $form = $this->createForm(EspecialidadType::class, $especialidad);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($especialidad);
            $entityManager->flush();

            return $this->redirectToRoute('app_especialidad_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('especialidad/new.html.twig', [
            'especialidad' => $especialidad,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_especialidad_show', methods: ['GET'])]
    public function show(Especialidad $especialidad): Response
    {
        return $this->render('especialidad/show.html.twig', [
            'especialidad' => $especialidad,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_especialidad_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Especialidad $especialidad, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EspecialidadType::class, $especialidad);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_especialidad_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('especialidad/edit.html.twig', [
            'especialidad' => $especialidad,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_especialidad_delete', methods: ['POST'])]
    public function delete(Request $request, Especialidad $especialidad, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$especialidad->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($especialidad);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_especialidad_index', [], Response::HTTP_SEE_OTHER);
    }
}
