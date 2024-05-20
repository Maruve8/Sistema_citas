<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Usuario;
use App\Form\PasswordResetRequestFormType;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Form\PasswordResetType;
use Symfony\Component\Mime\Email;

class RecuperaPassword extends AbstractController
{
    #[Route('/recuperarpassword', name: 'recuperar_password', methods: ['GET', 'POST'])]
    public function request(Request $request, MailerInterface $mailer, TokenGeneratorInterface $tokenGenerator, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PasswordResetRequestFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();
            $usuario = $entityManager->getRepository(Usuario::class)->findOneBy(['email' => $email]);

            if ($usuario) {
                $token = $tokenGenerator->generateToken();
                $usuario->setResetToken($token);
                $usuario->setResetTokenExpiresAt(new \DateTime('+1 hour')); // expiración del token
                $entityManager->flush();

                $resetUrl = $this->generateUrl('resetear_password', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);
                // Enviar email con el enlace para restablecer la contraseña
                $emailMessage = (new Email())
                    ->from('mv.contrerasbellido@gmail.com')
                    ->to($usuario->getEmail())
                    ->subject('Recuperar contraseña')
                    ->html($this->renderView('email/reset_password_email.html.twig', ['resetUrl' => $resetUrl]));

                $mailer->send($emailMessage);

                $this->addFlash('success', 'Se ha enviado un email para restablecer tu contraseña.');
                return $this->redirectToRoute('app_login');
            }

            $this->addFlash('error', 'No se ha encontrado ninguna cuenta para este correo electrónico.');
        }

        return $this->render('security/forgot_password.html.twig', [
            'requestForm' => $form->createView(),
        ]);
    }

    #[Route('/resetearpassword/{token}', name: 'resetear_password', methods: ['GET', 'POST'])]
    public function reset(Request $request, string $token, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
        $usuario = $entityManager->getRepository(Usuario::class)->findOneBy(['resetToken' => $token]);

        if (!$usuario) {
            $this->addFlash('error', 'Token inválido');
            return $this->redirectToRoute('recuperar_password');
        }

        // Verificar si el token ha expirado
        if ($usuario->getResetTokenExpiresAt() < new \DateTime()) {
            $this->addFlash('error', 'El token ha expirado');
            return $this->redirectToRoute('recuperar_password');
        }

        $form = $this->createForm(PasswordResetType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newPassword = $form->get('newPassword')->getData();
            $usuario->setPassword($passwordHasher->hashPassword($usuario, $newPassword));
            $usuario->setResetToken(null);
            $usuario->setResetTokenExpiresAt(null);
            $entityManager->flush();

            $this->addFlash('success', 'Su contraseña se ha restablecido correctamente.');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/reset_password.html.twig', [
            'resetForm' => $form->createView(),
            'token' => $token,
        ]);
    }
}


    