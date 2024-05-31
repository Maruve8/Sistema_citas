<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

//Pruebas de fallos con el envío de correos
class EmailTestController extends AbstractController
{
    #[Route('/test-email', name: 'test_email')]
    public function testEmail(MailerInterface $mailer): Response
    {
        $email = (new Email())
            ->from('citas.medicas.contacto@gmail.com')
            ->to('recipient@example.com')
            ->subject('Test Email')
            ->text('This is a test email.')
            ->html('<p>This is a test email.</p>');

        try {
            $mailer->send($email);
            return new Response('Email sent successfully');
        } catch (\Exception $e) {
            return new Response('Error sending email: ' . $e->getMessage());
        }
    }
}
