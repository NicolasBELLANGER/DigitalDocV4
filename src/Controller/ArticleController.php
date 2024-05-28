<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Commentaire;
use App\Entity\User;
use App\Form\FormulaireCreationArticleType;
use App\Form\FormulaireCreationCommentaireType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    #[Route('/articles', name: 'app_article')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User || (!$user->hasRole('Administrateur') && !$user->hasRole('Editeur') && !$user->hasRole('Viewer'))) {
            return $this->redirectToRoute('app_login');
        }
        $articles = $doctrine->getRepository(Article::class)->findAll();
        return $this->render('article/index.html.twig', [
            'articles' => $articles,
        ]);
    }

    #[Route('/articles/gestion', name: 'gestion_article')]
    public function gerer(ManagerRegistry $doctrine): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User || (!$user->hasRole('Administrateur') && !$user->hasRole('Editeur'))) {
            return $this->redirectToRoute('app_home');
        }
        $articles = $doctrine->getRepository(Article::class)->findAll();
        return $this->render('article/gestion.html.twig', [
            'articles' => $articles,
        ]);
    }

    #[Route('/articles/creation', name: 'article_creation')]
    public function add(ManagerRegistry $doctrine, Request $request)
    {
        $entityManager = $doctrine->getManager();

        $user = $this->getUser();
        if (!$user instanceof User || (!$user->hasRole('Administrateur') && !$user->hasRole('Editeur'))) {
            return $this->redirectToRoute('app_home');
        }

        $article = new Article();
        $article->setDateCreation(new \DateTimeImmutable());
        $article->setUser($user);

        $formArticle = $this->createForm(FormulaireCreationArticleType::class, $article);
        $formArticle->handleRequest($request);
        if($formArticle->isSubmitted() && $formArticle->isValid()){
            $entityManager->persist($article);
            $entityManager->flush();
            return $this->redirectToRoute('app_home');
        }
        return $this->render('article/form-add.html.twig', [
            'formArticle' => $formArticle->createView(),
        ]);
    }

    #[Route('/articles/modification/{id}', name: 'article_modification')]
    public function edit(ManagerRegistry $doctrine, $id, Request $request)
    {
        $user = $this->getUser();
        if (!$user instanceof User || (!$user->hasRole('Administrateur') && !$user->hasRole('Editeur'))) {
            return $this->redirectToRoute('app_home');
        }

        $entityManager = $doctrine->getManager();
        $article = $doctrine->getRepository(Article::class)->find($id);
        $article->setDateModification(new \DateTimeImmutable());

        $formArticle = $this->createForm(FormulaireCreationArticleType::class, $article);
        $formArticle->handleRequest($request);
        if($formArticle->isSubmitted() && $formArticle->isValid()){
            $entityManager->flush();
            return $this->redirectToRoute('app_home');
        }
        return $this->render('article/form-edit.html.twig', [
            'formArticle' => $formArticle->createView(),
        ]);
    }    

    #[Route('/articles/show/{id}', name: 'article_show')]
    public function show(ManagerRegistry $doctrine, Request $request, $id)
    {
        $user = $this->getUser();
        if (!$user instanceof User || (!$user->hasRole('Administrateur') && !$user->hasRole('Editeur') && !$user->hasRole('Viewer'))) {
            return $this->redirectToRoute('app_login');
        }

        $articles = $doctrine->getRepository(Article::class)->find($id);

        //Création du formulaire de commentaire
        $commentaire = new Commentaire();
        $formCommentaire = $this->createForm(FormulaireCreationCommentaireType::class, $commentaire);
        $formCommentaire->handleRequest($request);

        if($formCommentaire->isSubmitted() && $formCommentaire->isValid()){
            $commentaire->setArticle(($articles));
            $entityManager = $doctrine->getManager();
            $entityManager->persist($commentaire);
            $entityManager->flush();

            return $this->redirectToRoute('article_show', ['id' => $id]);
        }
        return $this->render('article/show.html.twig', [
            'articles' => $articles,
            'formCommentaire' => $formCommentaire->createView(),
        ]);
    }   

    #[Route('/articles/delete/{id}', name: 'article_delete')]
    public function delete(ManagerRegistry $doctrine, $id)
    {
        $user = $this->getUser();
        if (!$user instanceof User || (!$user->hasRole('Administrateur') && !$user->hasRole('Editeur'))) {
            return $this->redirectToRoute('app_home');
        }

        $article = $doctrine->getRepository(Article::class)->find($id);
        $entityManager = $doctrine->getManager();
        $entityManager->remove($article);
        $entityManager->flush();

        return $this->redirectToRoute('app_home');
    }  

    #[Route('/commentaire/delete/{id}', name: 'commentaire_delete')]
    public function deleteComment(ManagerRegistry $doctrine, $id)
    {
        $user = $this->getUser();
        if (!$user instanceof User || !$user->hasRole('Administrateur')) {
            return $this->redirectToRoute('app_home');
        }
        $commentaire = $doctrine->getRepository(Commentaire::class)->find($id);
        $articleId = $commentaire->getArticle()->getId();
        $entityManager = $doctrine->getManager();
        $entityManager->remove($commentaire);
        $entityManager->flush();

        return $this->redirectToRoute('article_show', ['id' => $articleId]);
    }  

}
