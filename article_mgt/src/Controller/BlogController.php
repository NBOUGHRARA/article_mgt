<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\ArticleLikes;
use App\Repository\ArticleRepository;
use App\Repository\ArticleLikesRepository;
use App\Form\ArticleType;
use App\Form\CommentType;

class BlogController extends AbstractController
{
    /**
     * @Route("/blog", name="blog")
     */
    public function index(ArticleRepository $repo)
    {
    	$articles = $repo->findAll();
        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
            'articles' => $articles,
        ]);
    }

    /** 
     * @Route("/", name="home")
     */
    public function home()
    {
    	return $this->render('blog/home.html.twig');
    }

    /**
     * @Route("/blog/new", name="blog_create")
     * @Route("/blog/{id}/edit", name="blog_edit")
     */
    public function form(Article $article = null, Request $request, ObjectManager $manager)
    {
    	if (!$article) {
    		$article = new Article();
    	}

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

    	return $this->render('blog/create.html.twig', [
    		'formArticle' => $form->createView(),
    		'editMode' => $article->getId() !== null
    	]);
    }

    /**
     * @Route("/blog/{id}", name="blog_show")
     */
    public function show(Article $article, Request $request, ObjectManager $manager)
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setCreatedAt(new \DateTime())
                    ->setArticle($article);

            $manager->persist($comment);
            $manager->flush();

            return $this->redirectToRoute('blog_show', ['id' => $article->getId()]);
        }

    	return $this->render('blog/show.html.twig', [
    		'article' => $article,
            'commentForm' => $form->createView()
    	]);
    }

    /**
     * @Route("/article/{id}/likes", name="article_likes")
     */
    public function likes(Article $article, Request $request, ObjectManager $manager, ArticleLikesRepository $likeRepo)
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->json([
                'code' => 403,
                'message' => 'vous ne pouvez pas réagir à un article sans que vous connectez'
            ], 200);
        }

        if ($article->isLikedByUser($user)) {
            $like = $likeRepo->findOneBy([
                'user' => $user,
                'article' => $article
            ]);

            $manager->remove($like);
            $manager->flush();

            return $this->json([
                'code' => 200,
                'message' => 'like bien supprimé',
                'likes' => $likeRepo->count(['article' => $article])
            ], 200);
        }

        $like = new ArticleLikes();
        $like->setUser($user)
            ->setArticle($article);
        $manager->persist($like);
        $manager->flush();

        return $this->json([
            'code' => 200,
            'message' => 'like bien enregistré',
            'likes' => $likeRepo->count(['article' => $article])
        ], 200);

    }
}
