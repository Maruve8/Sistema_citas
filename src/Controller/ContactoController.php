<?php

namespace App\Controller;

use App\Form\ContactoType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

#[Route('/contacto')]
class ContactoController extends AbstractController
{
    #[Route('/', name: 'app_contacto', methods: ['GET', 'POST'])]
    public function index(Request $request, MailerInterface $mailer): Response
    {
        $form = $this->createForm(ContactoType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $datos = $form->getData();

            // Enviar el correo electrónico
            $email = (new Email())
                ->from($datos['email'])
                ->to('mv.contrerasbellido@gmail.com') 
                ->subject('Formulario de Contacto')
                ->text(sprintf(
                    "Nombre: %s\nEmail: %s\nTeléfono: %s\n\nMensaje:\n%s",
                    $datos['nombre'],
                    $datos['email'],
                    $datos['telefono'],
                    $datos['mensaje']
                ));

            $mailer->send($email);

            $this->addFlash('success', 'Tu mensaje ha sido enviado.');
            

            return $this->redirectToRoute('app_contacto');
        }

        return $this->render('contacto/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}


