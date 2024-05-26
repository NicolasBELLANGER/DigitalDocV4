<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User || (!$user->hasRole('Administrateur') && !$user->hasRole('Editeur') && !$user->hasRole('Viewer'))) {
            return $this->redirectToRoute('app_login');
        }

        $articles = $doctrine->getRepository(Article::class)->findAll();
        return $this->render('home/index.html.twig', [
            'articles' => $articles,
        ]);
    }
}
