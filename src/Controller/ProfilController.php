<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Commentaire;
use App\Entity\User;
use App\Form\FormulaireCreationProfilType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class ProfilController extends AbstractController
{
    #[Route('/profil', name: 'app_profil')]
    public function index(): Response
    {
        $profils = $this->getUser();
        if (!$profils instanceof User || (!$profils->hasRole('Administrateur') && !$profils->hasRole('Editeur') && !$profils->hasRole('Viewer'))) {
            return $this->redirectToRoute('app_login');
        }
        return $this->render('profil/index.html.twig', [
            'profils' => $profils,
        ]);
    }

    #[Route('/profil/list', name: 'profil_list')]
    public function list(ManagerRegistry $doctrine)
    {
        $user = $this->getUser();
        if (!$user instanceof User || !$user->hasRole('Administrateur')) {
            return $this->redirectToRoute('app_home');
        }
        $profils = $doctrine->getRepository(User::class)->findAll();
        return $this->render('profil/list.html.twig', [
            'profils' => $profils,
        ]);
    }

    #[Route('/profil/modification/{id}', name: 'profil_modification')]
    public function edit(ManagerRegistry $doctrine, $id, Request $request, UserPasswordHasherInterface $profilPasswordHasher)
    {
        $user = $this->getUser();
        if (!$user instanceof User || !$user->hasRole('Administrateur')) {
            return $this->redirectToRoute('app_home');
        }

        $entityManager = $doctrine->getManager();
        $profil = $doctrine->getRepository(User::class)->find($id);
        $profil->setDateModification(new \DateTimeImmutable());

        $formProfil = $this->createForm(FormulaireCreationProfilType::class, $profil);
        $formProfil->handleRequest($request);

        if($formProfil->isSubmitted() && $formProfil->isValid()){
            $profil->setPassword(
                $profilPasswordHasher->hashPassword(
                    $profil,
                    $formProfil->get('password')->getData()
                )
            );
            $entityManager->persist($profil);
            $entityManager->flush();
            return $this->redirectToRoute('profil_list');
        }
        
        return $this->render('profil/form-edit.html.twig', [
            'formProfil' => $formProfil->createView(),
        ]);
    }

    #[Route('/profil/delete/{id}', name: 'profil_delete')]
    public function delete(ManagerRegistry $doctrine, $id)
    {
        $user = $this->getUser();
        if (!$user instanceof User || !$user->hasRole('Administrateur')) {
            return $this->redirectToRoute('app_home');
        }
        
        $profil = $doctrine->getRepository(User::class)->find($id);
        $entityManager = $doctrine->getManager();

        $commentaires = $doctrine->getRepository(Commentaire::class)->findBy(['user' => $profil]);
        foreach ($commentaires as $commentaire) {
            $entityManager->remove($commentaire);
        }
        $articles = $entityManager->getRepository(Article::class)->findBy(['user' => $profil]);
        foreach ($articles as $article) {
            $entityManager->remove($article);
        }
        $entityManager->remove($profil);
        $entityManager->flush();

        return $this->redirectToRoute('profil_list');
    }   
}
