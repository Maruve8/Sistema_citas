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
        // Aquí se puede almacenar la selección en la sesión o redirigir al siguiente paso para elegir la fecha.
        $data = $form->getData();
        $this->addFlash('success', 'Especialidad y médico seleccionados');

        //app_cita_calendar pdt agregar la ruta con el calendario
        return $this->redirectToRoute('app_cita_calendar', [
            'especialidadId' => $data['especialidad']->getId(),
            'medicoId' => $data['medico']->getId()
        ]);
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
