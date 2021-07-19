<?php

namespace App\Controller;
use DateTime;
use App\Entity\Article;
use App\Entity\Comment;
use App\Form\ArticleType;
use App\Form\CommentType;

use App\Repository\ArticleRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BlogController extends AbstractController
{
    #[Route('/', name: 'blog')]
    public function index(ArticleRepository $articleRepository,PaginatorInterface $paginator,Request $request)
    {  
        $articles = $paginator ->paginate($articleRepository->findAll(), 
        $request->query->getInt('page',1)
        ,
        5
        );
      
    
        return $this->render('blog/index.html.twig', [
            'articles' => $articles
        ]);
    }

    #[Route('article/new', name: 'new_article')]
    public function new(Request $request, FlashyNotifier $flashyNotifier)
    {   $article= new Article();
        $form= $this->createForm(ArticleType::class,$article);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isvalid()){
            $article->setcreatedAt(new DateTime());
            $article->setImage("https://picsum.photos/id/1/300/150");
            $entityManager=$this->getDoctrine()->getManager();
            $entityManager->persist($article);
            $entityManager->flush();
            $flashyNotifier->success('Article was added successfully');
            return $this->redirectToRoute("show_article",['id'=>$article->getId()]);
        }

        return $this->render('blog\new.html.twig',[
            'form'=>$form->createView()
        ]);
    }
    #[Route('article/{id}/edit', name: 'edit_article')]
    public function edit(Request $request, Article $article, FlashyNotifier $flashyNotifier): Response
    {
        $form= $this->createForm(ArticleType::class,$article);
        $form->handleRequest($request);
        if($form->isSubmitted()&& $form->isValid())
        {
            $entityManager=$this->getDoctrine()->getManager();
            $entityManager->persist($article);
            $entityManager->flush();
            $flashyNotifier->success('Article was modified successfully');

            return $this->redirectToRoute("show_article",['id'=>$article->getId()]);

        }
        return $this->render('blog/edit.html.twig',['editForm'=>$form->createView()
        ]);
    }

    #[Route('article/{id}', name: 'show_article', methods : ['GET','POST'] ) ]
    public function show(Article $article, Request $request, FlashyNotifier $flashyNotifier)
    {
       $comment= new Comment();
       $form = $this->createForm(CommentType::class,$comment);
       $form->handleRequest($request);
       if($form->isSubmitted()&& $form->isValid())
       {
        $comment->setCreatedAt(new DateTime());
        $comment->setArticle($article);
        $entityManager= $this->getDoctrine()->getManager();
           $entityManager->persist($comment);
           $entityManager->flush();
           $flashyNotifier->success('Your comment was added successfully');

           return $this->redirectToRoute("show_article",['id'=>$article->getId()]);

       }
        return $this->render('blog/show.html.twig',[
        'article' => $article,
        'commentForm'=>$form->createView(),
        ]);
    }
  

}
