<?php 

namespace App\Controller;

use App\Entity\Usuario;
use App\Form\AdminUserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Medico;
use App\Entity\Especialidad;
use App\Repository\UsuarioRepository;



class AdminController extends AbstractController
{
    #[Route('/admin/create', name: 'create_admin')]
    public function createAdmin(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $usuario = new Usuario();
        $form = $this->createForm(AdminUserType::class, $usuario);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            
            $hashedPassword = $passwordHasher->hashPassword(
                $usuario,
                $usuario->getPassword()
            );
            $usuario->setPassword($hashedPassword);

            $usuario->setRoles(['ROLE_ADMIN']);

            
            $entityManager->persist($usuario);
            $entityManager->flush();

            $this->addFlash('success', 'Usuario administrador creado exitosamente.');

            return $this->redirectToRoute('create_admin');
        }

        return $this->render('admin/create_admin.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    //vista inicio administrador
    #[Route('/admin', name: 'admin_dashboard')]
    public function dashboard(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        return $this->render('admin/dashboard.html.twig');
    }


    //vista admin usuarios (ver/editar/guardar)
    #[Route('/admin/usuarios', name: 'admin_usuarios')]
    public function index(UsuarioRepository $usuarioRepository): Response
    {
        $usuarios = $usuarioRepository->findAll();

        return $this->render('admin/usuarios.html.twig', [
            'usuarios' => $usuarios,
        ]);
    }

    #[Route('/admin/usuarios/{id}/editar', name: 'admin_usuarios_editar', methods: ['GET'])]
    public function editar(Usuario $usuario): Response
    {
        $data = [
            'id' => $usuario->getId(),
            'nombre' => $usuario->getNombre(),
            'apellidos' => $usuario->getApellidos(),
            'email' => $usuario->getEmail(),
            'telefono' => $usuario->getTelefono(),
            'dni' => $usuario->getDni(),
            'direccion' => $usuario->getDireccion()
        ];

        return $this->json($data);
    }

    #[Route('/admin/usuarios/guardar', name: 'admin_usuarios_guardar', methods: ['POST'])]
    public function guardar(Request $request, EntityManagerInterface $entityManager, UsuarioRepository $usuarioRepository): Response
    {
        $id = $request->request->get('id');
        $usuario = $usuarioRepository->find($id);

        if (!$usuario) {
            return $this->json(['success' => false, 'message' => 'Usuario no encontrado']);
        }

        $usuario->setNombre($request->request->get('nombre'));
        $usuario->setApellidos($request->request->get('apellidos'));
        $usuario->setEmail($request->request->get('email'));
        $usuario->setTelefono($request->request->get('telefono'));
        $usuario->setDni($request->request->get('dni'));
        $usuario->setDireccion($request->request->get('direccion'));

        $entityManager->persist($usuario);
        $entityManager->flush();

        return $this->json(['success' => true]);

    }

    



    //admin médicos (ver, editar, eliminar, añadir)
    #[Route('/admin/medicos', name: 'admin_medicos')]
    public function medicos(EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $medicos = $entityManager->getRepository(Medico::class)->findAll();
        return $this->render('admin/medicos.html.twig', ['medicos' => $medicos]);
    }

    #[Route('/admin/medicos/{id}/editar', name: 'admin_medicos_editar', methods: ['GET'])]
public function editarMedico(Medico $medico): Response
{
    $data = [
        'id' => $medico->getId(),
        'nombre' => $medico->getNombre(),
        'apellidos' => $medico->getApellidos(),
        'turno' => $medico->getTurno(),
        'especialidad' => [
            'id' => $medico->getEspecialidad()->getId(),
            'nombre' => $medico->getEspecialidad()->getNombre()
        ]
    ];

    return $this->json($data);
}

#[Route('/admin/medicos/guardar', name: 'admin_medicos_guardar', methods: ['POST'])]
public function guardarMedico(Request $request, EntityManagerInterface $em): Response
{
    $data = $request->request->all();
    $medicoId = $data['id'] ?? null;

    if ($medicoId) {
        $medico = $em->getRepository(Medico::class)->find($medicoId);
    } else {
        $medico = new Medico();
    }

    $especialidad = $em->getRepository(Especialidad::class)->find($data['especialidad']);

    if (!$especialidad) {
        return $this->json(['success' => false, 'message' => 'Especialidad no encontrada']);
    }

    $medico->setNombre($data['nombre']);
    $medico->setApellidos($data['apellidos']);
    $medico->setTurno($data['turno']);
    $medico->setEspecialidad($especialidad);

    $em->persist($medico);
    $em->flush();

    return $this->json(['success' => true]);
}

#[Route('/admin/medicos/{id}/eliminar', name: 'admin_medicos_eliminar', methods: ['POST'])]
public function eliminarMedico(Medico $medico, EntityManagerInterface $em): Response
{
    $em->remove($medico);
    $em->flush();

    return $this->json(['success' => true]);
}


    //admin especialidades (ver, editar, eliminar, añadir)
    #[Route('/admin/especialidades', name: 'admin_especialidades')]
public function especialidades(EntityManagerInterface $entityManager): Response
{
    $this->denyAccessUnlessGranted('ROLE_ADMIN');
    $especialidades = $entityManager->getRepository(Especialidad::class)->findAll();
    return $this->render('admin/especialidades.html.twig', ['especialidades' => $especialidades]);
}

#[Route('/admin/especialidades/{id}/editar', name: 'admin_especialidades_editar', methods: ['GET'])]
public function editarEspecialidad(Especialidad $especialidad): Response
{
    $data = [
        'id' => $especialidad->getId(),
        'nombre' => $especialidad->getNombre(),
        'descripcion' => $especialidad->getDescripción()
    ];

    return $this->json($data);
}



#[Route('/admin/especialidades/guardar', name: 'admin_especialidades_guardar', methods: ['POST'])]
public function guardarEspecialidad(Request $request, EntityManagerInterface $em): Response
{
    $data = $request->request->all();
    $especialidadId = $data['id'] ?? null;

    if ($especialidadId) {
        $especialidad = $em->getRepository(Especialidad::class)->find($especialidadId);
    } else {
        $especialidad = new Especialidad();
    }

    $especialidad->setNombre($data['nombre']);
    $especialidad->setDescripción($data['descripcion']);

    $em->persist($especialidad);
    $em->flush();

    return $this->json(['success' => true]);
}


#[Route('/admin/especialidades/{id}/eliminar', name: 'admin_especialidades_eliminar', methods: ['POST'])]
public function eliminarEspecialidad(Especialidad $especialidad, EntityManagerInterface $em): Response
{
    $em->remove($especialidad);
    $em->flush();

    return $this->json(['success' => true]);
}





}