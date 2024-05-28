<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentaireController extends AbstractController
{
    #[Route('/commentaire/delete/{id}', name: 'commentaire_profil')]
    public function delete(ManagerRegistry $doctrine, $id)
    {
        $user = $this->getUser();
        if (!$user instanceof User || !$user->hasRole('Administrateur')) {
            return $this->redirectToRoute('app_home');
        }
        $commentaire = $doctrine->getRepository(Commentaire::class)->find($id);
        $entityManager = $doctrine->getManager();
        $entityManager->remove($commentaire);
        $entityManager->flush();

        return $this->redirectToRoute('profil/show.html.twig');
    }  
}
