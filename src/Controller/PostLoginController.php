<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class PostLoginController extends AbstractController
{
    #[Route('/post_login', name: 'post_login')]
    public function index(AuthorizationCheckerInterface $authorizationChecker)
    {
        //si admin o user inicio
        if ($authorizationChecker->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('admin_dashboard');
        }

        return $this->redirectToRoute('app_inicio');
    }
}
