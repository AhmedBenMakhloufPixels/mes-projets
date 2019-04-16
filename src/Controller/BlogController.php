<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Article;
use App\Repository\ArticleRepository;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatorInterface;
use App\Form\ArticleType;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\JsonResponse;

class BlogController extends Controller
{
    /**
     * @Route("/blog", name="blog")
     */
    public function index(Request $request)
    {

        //$repo=$this->getDoctrine()->getRepository(Article::class);
       // $articles = $repo->findAll();
        $em = $this->getDoctrine()->getManager();
        $paginator = $this->get('knp_paginator');
        $articles = $paginator->paginate(
        $em->getRepository(Article::class)->findAll(), /* query NOT result */ $request->query->getInt('page', 1)/* page number */, 5/* limit per page */
        );
       

      
        
        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
            'articles'=> $articles,
            
        ]);
    }
        

/**
 * @Route("/",name="home")
*/

    public function home(){

    	return $this->render('blog/home.html.twig');
    }



     /**
     * @Route("/blog/new",name="blog_new")
     */
    public function new(Request $request, ObjectManager $manager){

        $article = new Article();

       /* $form= $this->createFormBuilder($article)
                    ->add('title')
                    ->add('content')
                    ->add('image')
                    ->getForm();*/

        $form= $this->createForm(ArticleType::class,$article);            


                    $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                        # code...
                        $article->setCreatedAt(new \DateTime());
                        $manager->persist($article);
                        $manager->flush($article);
                        $normalizer = new ObjectNormalizer();
                        $normalizer->setCircularReferenceLimit(1);
                        $serializer = new Serializer([$normalizer]);
                        $normalizer->setCircularReferenceHandler(function ($object) {
                            return $object->getId();
                        });
                        $formatted= $serializer->normalize($article, 'json');
                        return new JsonResponse($formatted);

                 return $this->redirectToRoute('blog_show', ['id'=> $article->getId()]);      
                    }        
        return $this->render('blog/new.html.twig',[
            'formArticle' => $form->createView()
        ]);




    }

    /**
     * @Route("/blog/{id}/edit",name="blog_edit")
     */

     public function edit(Article $article, Request $request, ObjectManager $manager){

        

       /* $form= $this->createFormBuilder($article)
                    ->add('title')
                    ->add('content')
                    ->add('image')
                    ->getForm();*/


        $form= $this->createForm(ArticleType::class,$article);             

                    $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                        # code...
                        
                        $manager->persist($article);
                        $manager->flush();

                 return $this->redirectToRoute('blog_show', ['id'=> $article->getId()]);      
                    }        
        return $this->render('blog/edit.html.twig',[
            'formArticle' => $form->createView()
        ]);




    }


    

    /**
     * @Route("/blog/{id}",name="blog_show")
     */

    public function show($id){


        $repo=$this->getDoctrine()->getRepository(Article::class);
        $article = $repo->find($id);

        return $this->render('blog/show.html.twig',[
            'article'=> $article
        ]);
    }


    /**
     * @Route("/blog/{id}/delete",name="blog_delete")
     */

    public function delete(Article $article, Request $request, ObjectManager $manager,$id){
            $manager->remove($article);
            $manager->flush();
            return $this->render('blog/home.html.twig');
    }

  
}
