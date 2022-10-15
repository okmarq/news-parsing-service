<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class AuthController extends AbstractController
{
    /**
     * @Route("/", name="app_auth")
     */
    public function index(): Response
    {
        return $this->render('auth/index.html.twig', [
            'controller_name' => 'AuthController',
        ]);
    }

    /**
     * @Route("/admin", name="admin")
     */
    public function admin(SessionInterface $session): Response
    {
        $session->set('role', 'Admin');

        return $this->redirectToRoute('app_news_index');
    }

    /**
     * @Route("/moderator", name="moderator")
     */
    public function moderator(SessionInterface $session): Response
    {
        $session->set('role', 'Moderator');

        return $this->redirectToRoute('app_news_index');
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout(SessionInterface $session): Response
    {
        $session->set('role', null);

        return $this->redirectToRoute('app_auth');
    }
}
