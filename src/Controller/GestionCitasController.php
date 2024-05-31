<?php

namespace App\Controller;

use App\Repository\CitaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;



#[Route('/gestiona')]
class GestionCitasController extends AbstractController
{
    #[Route('/', name: 'app_gestiona_citas')]
    public function index(CitaRepository $citaRepository): Response
    {
        $user = $this->getUser();

        $citasPendientes = $citaRepository->findBy(['paciente' => $user, 'estado' => 'Confirmada']);
        $historicoCitas = $citaRepository->findBy(['paciente' => $user, 'estado' => 'Finalizada']);
        $citasAnuladas = $citaRepository->findBy(['paciente' => $user, 'estado' => 'Cancelada']);

        return $this->render('gestiona/index.html.twig', [
            'citasPendientes' => $citasPendientes,
            'historicoCitas' => $historicoCitas,
            'citasAnuladas' => $citasAnuladas,
        ]);
    }

    #[Route('/cancelar/{id}', name: 'app_cancelar_cita')]
    public function cancelar(int $id, EntityManagerInterface $entityManager, CitaRepository $citaRepository): Response
    {
        $cita = $citaRepository->find($id);
        if ($cita && $cita->getPaciente() === $this->getUser() && $cita->getEstado() === 'Confirmada') {
            $cita->setEstado('Cancelada');
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_gestiona_citas');
    }
}