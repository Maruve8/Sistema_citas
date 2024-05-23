<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\CitaRepository;


class AdminDashboardController extends AbstractController
{
    #[Route('/admin/dashboard', name: 'admin_dashboard')]
    public function index(CitaRepository $citaRepository): Response
    {
        $datosCitas = $this->obtenerDatosCitas($citaRepository);

        return $this->render('admin/dashboard.html.twig', [
            'datosCitas' => json_encode($datosCitas),
        ]);
    }

    private function obtenerDatosCitas(CitaRepository $citaRepository): array
    {
        $datos = [];
        $diasSemana = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        foreach ($diasSemana as $dia) {
            $datos[$dia] = 0;
        }

        $fechaInicio = new \DateTime('monday this week');
        $fechaFin = new \DateTime('sunday this week');

        $citas = $citaRepository->createQueryBuilder('c')
            ->where('c.fechaHora BETWEEN :fechaInicio AND :fechaFin')
            ->setParameter('fechaInicio', $fechaInicio->format('Y-m-d 00:00:00'))
            ->setParameter('fechaFin', $fechaFin->format('Y-m-d 23:59:59'))
            ->getQuery()
            ->getResult();

        foreach ($citas as $cita) {
            $diaSemana = $cita->getFechaHora()->format('l');
            if (!isset($datos[$diaSemana])) {
                $datos[$diaSemana] = 0;
            }
            $datos[$diaSemana]++;
        }

        return $datos;
    }
}



