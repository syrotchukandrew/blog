<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Post;
use AppBundle\Entity\Tag;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\CommentType;
use AppBundle\Form\MarkType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;

class BlogController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {

        $posts = $this->getDoctrine()->getRepository('AppBundle:Post')->queryLatest();
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $posts,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('blog/index.html.twig', array('pagination' => $pagination));
    }

    /**
     * @Route("/post/{slug}", name="blog_post", options={"expose"=true})
     */
    public function postShowAction(Post $post, Request $request)
    {
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $comments = $post->getComments(),
            $request->query->getInt('page', 1),
            5
        );
        return $this->render('blog/post_show.html.twig', array('pagination' => $pagination, 'post' => $post));
    }

    /**
     * @Route("/comment/{postSlug}/new", name = "comment_new")
     * @Method("POST")
     * @ParamConverter("post", options={"mapping": {"postSlug": "slug"}})
     */
    public function commentNewAction(Post $post, Request $request)
    {
        $form = $this->createForm(CommentType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $comment = $form->getData();
            $comment->setPost($post);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('blog_post', array('slug' => $post->getSlug()));
        }

        return $this->render('blog/_comment_form.html.twig', array(
            'post' => $post,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/mark/{postSlug}/new", name = "mark_new")
     * @Method("POST")
     * @ParamConverter("post", options={"mapping": {"postSlug": "slug"}})
     */
    public function markNewAction(Post $post, Request $request)
    {
        $form = $this->createForm(MarkType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $mark = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();
            $post->addMark($mark['mark']);
            $entityManager->flush();

            return $this->redirectToRoute('blog_post', array('slug' => $post->getSlug()));
        }

        return $this->render('blog/_mark_form.html.twig', array(
            'post' => $post,
            'form' => $form->createView(),
        ));
    }

    public function sidebarAction()
    {
        $em = $this->getDoctrine()->getManager();
        $tagWeights = $this->get('app.tag_weights')->tagWeights();
        $latestComments = $em->getRepository('AppBundle:Comment')->getLatestComments();
        $populatePosts = $em->getRepository('AppBundle:Post')->getPopulate();

        return $this->render("blog/sidebar.html.twig", array(
            'latestComments' => $latestComments,
            'alltags' => $tagWeights,
            'populatePosts' => $populatePosts
        ));
    }

    /**
     * @Route("/tag/{slug}", name="tag_post")
     */
    public function tagShowAction($slug, Request $request)
    {
        $tag = $this->getDoctrine()->getRepository('AppBundle:Tag')->getTagWithPosts($slug);
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $posts = $tag[0]->getPosts(),
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('blog/tag_show.html.twig', array('pagination' => $pagination, 'tag' => $tag));
    }

    /**
     * @Route("/livesearch", name="livesearch", options={"expose"=true})
     */
    public function livesearchAction(Request $request)
    {
        if ($request->getMethod() === 'GET') {
            return new Response(json_encode($this->get('app.searcher')->search()));
        }
        if ($request->getMethod() === 'POST') {
            $paginator = $this->get('knp_paginator');
            $pagination = $paginator->paginate(
                $this->get('app.searcher')->search(),
                $request->query->getInt('page', 1),
                10
            );
            return $this->render('blog/index.html.twig', array('pagination' => $pagination));
        }
    }
}
