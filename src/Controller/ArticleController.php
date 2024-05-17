<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\FormulaireCreationArticleType;
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
        $articles = $doctrine->getRepository(Article::class)->findAll();
        return $this->render('article/index.html.twig', [
            'articles' => $articles,
        ]);
    }

    #[Route('/articles/gestion', name: 'gerer_article')]
    public function gerer(ManagerRegistry $doctrine): Response
    {
        $articles = $doctrine->getRepository(Article::class)->findAll();
        return $this->render('article/gerer.html.twig', [
            'articles' => $articles,
        ]);
    }

    #[Route('/articles/creation', name: 'article_creation')]
    public function add(ManagerRegistry $doctrine, Request $request)
    {
        $entityManager = $doctrine->getManager();

        $user = $this->getUser();

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
    public function show(ManagerRegistry $doctrine, $id)
    {
        $articles = $doctrine->getRepository(Article::class)->find($id);
        return $this->render('article/show.html.twig', [
            'articles' => $articles,
        ]);
    }   

    #[Route('/articles/delete/{id}', name: 'article_delete')]
    public function delete(ManagerRegistry $doctrine, $id)
    {
        $article = $doctrine->getRepository(Article::class)->find($id);
        $entityManager = $doctrine->getManager();
        $entityManager->remove($article);
        $entityManager->flush();

        return $this->redirectToRoute('app_home');
    }  

}
