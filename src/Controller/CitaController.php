<?php

namespace App\Controller;

use App\Entity\Cita;
use App\Form\CitaType;
use App\Repository\CitaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/cita')]
class CitaController extends AbstractController
{
    #[Route('/', name: 'app_cita_index', methods: ['GET'])]
    public function index(CitaRepository $citaRepository): Response
    {
        return $this->render('cita/index.html.twig', [
            'citas' => $citaRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_cita_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $citum = new Cita();
        $form = $this->createForm(CitaType::class, $citum);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($citum);
            $entityManager->flush();

            return $this->redirectToRoute('app_cita_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('cita/new.html.twig', [
            'citum' => $citum,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_cita_show', methods: ['GET'])]
    public function show(Cita $citum): Response
    {
        return $this->render('cita/show.html.twig', [
            'citum' => $citum,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_cita_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Cita $citum, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CitaType::class, $citum);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_cita_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('cita/edit.html.twig', [
            'citum' => $citum,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_cita_delete', methods: ['POST'])]
    public function delete(Request $request, Cita $citum, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$citum->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($citum);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_cita_index', [], Response::HTTP_SEE_OTHER);
    }
}
