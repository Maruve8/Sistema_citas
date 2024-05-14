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

    // Pasar los datos necesarios para el calendario a la vista
    return $this->render('cita/calendar.html.twig', [
        'especialidadId' => $especialidadId,
        'medicoId' => $medicoId,
        'especialidad' => $especialidad,
        'medico' => $medico
    ]);
}


//manejar petición ajax que busca horas disponibles para fecha seleccionada
#[Route('/horas-disponibles', name: 'ruta_para_horas_disponibles')]
public function getAvailableHours(Request $request, EntityManagerInterface $entityManager): Response
{
    $fecha = new \DateTime($request->query->get('fecha'));
    $especialidadId = $request->query->get('especialidadId');
    $medicoId = $request->query->get('medicoId');
    // Consultar las horas disponibles para esa fecha
    $horasDisponibles = $entityManager->getRepository(Cita::class)->findAvailableTimesByDate($fecha, $medicoId, $especialidadId);
    

    // Renderiza una vista con las horas
    return $this->json(['horas' => $horasDisponibles]);
}


#[Route('/confirmar-cita/{especialidadId}/{medicoId}/{fecha}/{hora}', name: 'app_cita_confirmar')]
public function confirmarCita(EntityManagerInterface $entityManager, $especialidadId, $medicoId, $fecha, $hora): Response
{
    try {
        $fechaHora = new \DateTime("$fecha $hora");
    } catch (\Exception $e) {
        
       throw new \Exception("Invalid date or time format: " . $e->getMessage());
    }

    $especialidad = $entityManager->getRepository(Especialidad::class)->find($especialidadId);
    $medico = $entityManager->getRepository(Medico::class)->find($medicoId);

    return $this->render('cita/confirmarCita.html.twig', [
        'especialidad' => $especialidad,
        'medico' => $medico,
        'fechaHora' => $fechaHora,
    ]);
}

#[Route('/cita/success/{especialidadId}/{medicoId}/{fechaHora}', name: 'app_cita_success')]
public function citaSuccess(EntityManagerInterface $entityManager, int $especialidadId, int $medicoId, string $fechaHora): Response
{
    $especialidad = $entityManager->getRepository(Especialidad::class)->find($especialidadId);
    $medico = $entityManager->getRepository(Medico::class)->find($medicoId);
    $fechaHora = \DateTime::createFromFormat('Y-m-d H:i', $fechaHora);

    return $this->render('cita/success_cita.html.twig', [
        'especialidad' => $especialidad,
        'medico' => $medico,
        'fechaHora' => $fechaHora,
    ]);
}

    

}
