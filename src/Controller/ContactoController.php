<?php

namespace App\Controller;

use App\Form\ContactoType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Mailer\Messenger\SendEmailMessage;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mime\Email;

#[Route('/contacto')]
class ContactoController extends AbstractController
{
    #[Route('/', name: 'app_contacto', methods: ['GET', 'POST'])]
    public function index(Request $request, MessageBusInterface $bus, LoggerInterface $logger): Response
    {
        $form = $this->createForm(ContactoType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $datos = $form->getData();

            // Crear el correo electrónico
            $email = (new Email())
                ->from($datos['email'])
                ->to('citas.medicas.contacto@gmail.com')
                ->subject('Formulario de Contacto')
                ->text(sprintf(
                    "Nombre: %s\nEmail: %s\nTeléfono: %s\n\nMensaje:\n%s",
                    $datos['nombre'],
                    $datos['email'],
                    $datos['telefono'],
                    $datos['mensaje']
                ));

            try {
                // Enviar el correo electrónico a la cola del worker
                $bus->dispatch(new SendEmailMessage($email));
                $logger->info('Correo enviado a la cola para procesar', ['email' => $email]);

                $this->addFlash('success', 'Tu mensaje ha sido enviado.');
            } catch (\Exception $e) {
                $logger->error('Error al enviar correo.', ['exception' => $e]);
                $this->addFlash('danger', 'Hubo un error al enviar tu mensaje.');
            }

            return $this->redirectToRoute('app_contacto');
        }

        return $this->render('contacto/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}







