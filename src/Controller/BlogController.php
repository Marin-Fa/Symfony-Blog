<?php

namespace App\Controller;

use App\Entity\Articles;
use App\Entity\Comment;
use App\Form\ArticleType;
use App\Form\CommentType;
use App\Repository\ArticlesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class BlogController extends AbstractController
{
    /**
     * @Route("/blog", name="blog")
     * @param ArticlesRepository $repo
     * @return Response
     */
    public function index(ArticlesRepository $repo)
    {
        $repo = $this->getDoctrine()->getRepository(Articles::class);
        // $article = $repo->find(10);
        // $article = $repo->findOneByTitle('Titre de l\'article');
        // $articles = $repo->findByTitle('Titre de l\'article');
        $articles = $repo->findAll();

        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
            'articles' => $articles
        ]);
    }
    /**
     * 1ERE function
     * @Route("/", name="home")
     * 1ERE vue
     * variables : title et age
     */
    public function home()
    {
        return $this->render('blog/home.html.twig', [
            'title' => "Bienvenur les amis",
            'age' => 31
        ]);
    }

    /**
     * @Route("/blog/new", name="blog_create")
     * @Route("/blog/{id}/edit", name="blog_edit")
     * @param Articles|null $article
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return RedirectResponse|Response
     * @throws Exception
     */
    public function form(Articles $article = null, Request $request, EntityManagerInterface $manager)
    {
        if (!$article) {
            $article = new Articles();
        }
        // Sans passer par la class crée dans la console
        // $form = $this->createFormBuilder($article)
        //              ->add('title')
        //              ->add('content')
        //              ->add('image')
        //              ->getForm();

        // En passant par la classe crée dans la connsole (class, entité)
        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$article->getId()) {
                $article->setCreatedAt(new \DateTime());
            }

            $manager->persist($article);
            $manager->flush();
            return $this->redirectToRoute('blog_show', ['id' => $article->getId()]);
        }
        // dump($article);
        return $this->render('blog/create.html.twig', [
            'formArticle' => $form->createView(),
            // Pour le bouton du from ISERT || UPDATE
            'editMode' => $article->getId() !== null
        ]);
    }

    /**
     * @Route("/blog/{id}", name="blog_show")
     * @param Articles $article
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return RedirectResponse|Response
     * @throws Exception
     */
    public function show(Articles $article, Request $request, EntityManagerInterface $manager)
    {
        $comment = new Comment();

        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setCreatedAt(new \DateTime())
                    ->setArticle($article);

            $manager->persist($comment);
            $manager->flush();

            dump($comment);
            return $this->redirectToRoute('blog_show', ['id' => $article->getId()]);
            }
        // $repo = $this->getDoctrine()->getRepository(Articles::class);
        // $article = $repo->find($id);
        return $this->render('blog/show.html.twig', [
            'article' => $article,
            'commentForm' => $form->createView()
        ]);
    }
}

