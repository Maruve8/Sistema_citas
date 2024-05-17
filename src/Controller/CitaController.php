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
use App\Form\CitaOnlineType; 
use App\Repository\MedicoRepository;
use App\Entity\Medico;
use App\Entity\Especialidad;
use App\Form\CitaFechaFormType;
use App\Entity\Usuario;



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

    //método select para cita online, elegir especialidad y médico
#[Route('/citaonline', name: 'app_cita_online', methods: ['GET', 'POST'])]
public function select(Request $request, EntityManagerInterface $entityManager): Response
{
    
    $form = $this->createForm(CitaOnlineType::class); 
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $especialidad = $form->get('especialidad')->getData(); // Obtenemos la especialidad seleccionada del formulario
        $medico = $form->get('medico')->getData(); // Obtenemos el médico seleccionado del formulario

        // Asegurarse de que tanto la especialidad como el médico no son nulos
        if ($especialidad && $medico) {
            return $this->redirectToRoute('app_cita_calendar', [
                'especialidadId' => $especialidad->getId(),
                'medicoId' => $medico->getId(),
            ]);
        } else {
            $this->addFlash('error', 'Especialidad o médico no seleccionado correctamente.');
        }
    } else {
        $this->addFlash('error', 'Hay errores en el formulario. Por favor, revíselo.');
    }


    return $this->render('cita/citaonline.html.twig', [
        'citaonlineForm' => $form->createView(),
    ]);
}


 //manejar la solicitud ajax, devuelve médicos disponibles por especialidad seleccionada
 #[Route('/citamedico', name: 'app_cita_medico', methods: ['POST'])]
 public function medicosPorEspecialidad(Request $request, MedicoRepository $medicoRepository): Response
 {
     $especialidadId = $request->request->get('especialidad_id');
     $medicos = $medicoRepository->findBy(['especialidad' => $especialidadId]);

     $medicosArray = [];
  foreach ($medicos as $medico) {
         $medicosArray[] = [
             'id' => $medico->getId(),
             'nombre' => $medico->getNombre() . ' ' . $medico->getApellidos()
         ];
     }
     
     return $this->json(['medicos' => $medicosArray]);
 }


 #[Route('/calendar/{especialidadId}/{medicoId}', name: 'app_cita_calendar')]
public function calendar(EntityManagerInterface $entityManager, int $especialidadId, int $medicoId): Response
{
    // Obtener entidades de la base de datos
    $especialidad = $entityManager->getRepository(Especialidad::class)->find($especialidadId);
    $medico = $entityManager->getRepository(Medico::class)->find($medicoId);

    // Comprobar si la especialidad y el médico existen
    if (!$especialidad || !$medico) {
        $this->addFlash('error', 'Especialidad o médico no válido.');
        return $this->redirectToRoute('app_cita_online'); 
    }

    //usuario autenticado
    $usuario = $this->getUser();  
    if (!$usuario) {
        $this->addFlash('error', 'Debe iniciar sesión para acceder a esta página.');
        return $this->redirectToRoute('app_login');  
    }

    

    // Pasar los datos necesarios para el calendario a la vista
    return $this->render('cita/calendar.html.twig', [
        'especialidadId' => $especialidadId,
        'medicoId' => $medicoId,
        'especialidad' => $especialidad,
        'medico' => $medico,
        'usuario' => $usuario
        
    ]);
}


//manejar petición ajax que busca horas disponibles para fecha seleccionada
#[Route('/horas-disponibles', name: 'ruta_para_horas_disponibles')]
public function getAvailableHours(Request $request, EntityManagerInterface $entityManager): Response
{
    $fecha = new \DateTime($request->query->get('fecha'));
    $especialidadId = $request->query->get('especialidadId');
    $medicoId = $request->query->get('medicoId');

    $startOfDay = clone $fecha;
    $startOfDay->setTime(0, 0, 0);
    $endOfDay = clone $fecha;
    $endOfDay->setTime(23, 59, 59);

    $dql = "SELECT c.fechaHora 
            FROM App\Entity\Cita c 
            WHERE c.fechaHora BETWEEN :startOfDay AND :endOfDay 
            AND c.medico = :medicoId 
            AND c.especialidad = :especialidadId 
            AND c.estado != :estadoConfirmada";

    error_log("Query DQL: $dql");
    error_log("Parameters: startOfDay = " . $startOfDay->format('Y-m-d H:i:s') . ", endOfDay = " . $endOfDay->format('Y-m-d H:i:s') . ", medicoId = $medicoId, especialidadId = $especialidadId, estadoConfirmada = 'Confirmada'");

    $query = $entityManager->createQuery($dql)
        ->setParameter('startOfDay', $startOfDay)
        ->setParameter('endOfDay', $endOfDay)
        ->setParameter('medicoId', $medicoId)
        ->setParameter('especialidadId', $especialidadId)
        ->setParameter('estadoConfirmada', 'Confirmada');

    $takenTimes = $query->getResult();

    error_log('Resultados de la consulta DQL: ' . print_r($takenTimes, true));

    $availableTimes = $this->calculateAvailableTimes($fecha, $medicoId, $entityManager, $takenTimes);

    return $this->json(['horas' => $availableTimes]);
}


private function calculateAvailableTimes($fecha, $medicoId, $entityManager, $takenTimes)
{
    $medico = $entityManager->getRepository(Medico::class)->find($medicoId);
    $turno = $medico->getTurno();

    $startTime = ($turno === 'manana') ? 8 : 15;
    $endTime = ($turno === 'manana') ? 14 : 20;

    $startDateTime = clone $fecha;
    $startDateTime->setTime($startTime, 0);
    $endDateTime = clone $fecha;
    $endDateTime->setTime($endTime, 45);

    $interval = new \DateInterval('PT15M');
    $period = new \DatePeriod($startDateTime, $interval, $endDateTime);

    $now = new \DateTime();
    if ($fecha->format('Y-m-d') === $now->format('Y-m-d')) {
        $now = $now->format('H:i:s');
    } else {
        $now = '00:00:00';
    }

    $availableTimes = [];
    foreach ($period as $dt) {
        if ($dt->format('H:i:s') > $now) {
            $availableTimes[] = $dt->format('H:i:s');
        }
    }

    $takenHours = array_map(function ($entry) {
        return $entry['fechaHora']->format('H:i:s');
    }, $takenTimes);

    error_log('Horas ocupadas: ' . print_r($takenHours, true));
    error_log('Horas disponibles antes del filtrado: ' . print_r($availableTimes, true));

    $availableTimes = array_diff($availableTimes, $takenHours);

    error_log('Horas disponibles después del filtrado: ' . print_r($availableTimes, true));

    return $availableTimes;
}



#[Route('/confirmar-cita/{especialidadId}/{medicoId}/{usuarioId}/{fecha}/{hora}', name: 'app_cita_confirmar')]
public function confirmarCita(EntityManagerInterface $entityManager, $especialidadId, $medicoId, $usuarioId, $fecha, $hora): Response
{
    $fechaHora = new \DateTime("$fecha $hora");

    $especialidad = $entityManager->getRepository(Especialidad::class)->find($especialidadId);
    $medico = $entityManager->getRepository(Medico::class)->find($medicoId);
    $usuario = $entityManager->getRepository(Usuario::class)->find($usuarioId);

    return $this->render('cita/confirmarCita.html.twig', [
        'especialidad' => $especialidad,
        'medico' => $medico,
        'usuario' => $usuario,
        'fechaHora' => $fechaHora,
    ]);
}

#[Route('/confirmar-cita/guardar', name: 'app_cita_guardar', methods: ['POST'])]
public function guardarCita(Request $request, EntityManagerInterface $entityManager): Response
{
    $especialidadId = $request->request->get('especialidadId');
    $medicoId = $request->request->get('medicoId');
    $usuarioId = $request->request->get('usuarioId');
    $fecha = $request->request->get('fecha');
    $hora = $request->request->get('hora');

    $fechaHora = new \DateTime("$fecha $hora");

    $especialidad = $entityManager->getRepository(Especialidad::class)->find($especialidadId);
    $medico = $entityManager->getRepository(Medico::class)->find($medicoId);
    $usuario = $entityManager->getRepository(Usuario::class)->find($usuarioId);

    // Crear una nueva cita
    $cita = new Cita();
    $cita->setFechaHora($fechaHora);
    $cita->setMedico($medico);
    $cita->setEspecialidad($especialidad);
    $cita->setPaciente($usuario);
    $cita->setEstado(Cita::ESTADO_CONFIRMADA);

    // Persistir la nueva cita en la base de datos
    $entityManager->persist($cita);
    $entityManager->flush();

    return $this->redirectToRoute('app_cita_success', [
        'especialidadId' => $especialidadId,
        'medicoId' => $medicoId,
        'fechaHora' => $fechaHora->format('Y-m-d H:i:s')
    ]);
}

#[Route('/cita/success/{especialidadId}/{medicoId}/{fechaHora}', name: 'app_cita_success')]
public function citaSuccess(EntityManagerInterface $entityManager, int $especialidadId, int $medicoId, string $fechaHora): Response
{
    $especialidad = $entityManager->getRepository(Especialidad::class)->find($especialidadId);
    $medico = $entityManager->getRepository(Medico::class)->find($medicoId);
    $fechaHora = \DateTime::createFromFormat('Y-m-d H:i:s', $fechaHora);

    return $this->render('cita/success_cita.html.twig', [
        'especialidad' => $especialidad,
        'medico' => $medico,
        'fechaHora' => $fechaHora,
    ]);
}

    

}
