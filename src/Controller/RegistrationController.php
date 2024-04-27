<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request; 
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\Usuario;  
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;



class RegistrationController extends AbstractController
{
    #[Route('/registro', name: 'app_registro')]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new Usuario();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Codificar la contraseña utilizando UserPasswordHasherInterface
            $user->setPassword(
                $passwordHasher->hashPassword($user, $form->get('password')->get('first')->getData())
            );

            // Guardar el nuevo usuario utilizando el entity manager inyectado
            $entityManager->persist($user);
            $entityManager->flush();

            // Redirigir a la ruta de login 
            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/index.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}

