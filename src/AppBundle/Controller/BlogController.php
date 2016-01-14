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


/**
 * @Route("/blog")
*/
class BlogController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {

        $posts = $this->getDoctrine()->getRepository('AppBundle:Post')->queryLatest();
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $posts,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('blog/index.html.twig', array('pagination' => $pagination));
    }

    /**
     * @Route("/posts/{slug}", name="blog_post")
     */
    public function postShowAction(Post $post)
    {
        return $this->render('blog/post_show.html.twig', array('post' => $post));
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
            $entityManager->persist($post);
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
        $tags = $em->getRepository('AppBundle:Tag')->getTags();
        $tagWeights = $this->getDoctrine()->getRepository('AppBundle:Tag')->getTagWeights($tags);
        $latestComments = $em->getRepository('AppBundle:Comment')->getLatestComments();
        $populatePosts = $em->getRepository('AppBundle:Post')->getPopulate();

        return $this->render("blog/sidebar.html.twig", array(
            'latestComments'    => $latestComments,
            'alltags' => $tagWeights,
            'populatePosts' => $populatePosts
        ));
    }

    /**
     * @Route("/tag/{slug}", name="tag_post")
     */
    public function tagShowAction($slug)
    {
        $tag = $this->getDoctrine()->getRepository('AppBundle:Tag')->getTagWithPosts($slug);
        return $this->render('blog/tag_show.html.twig', array('tag' => $tag));
    }

    /**
     * @Route("/livesearch/{slug}", name="livesearch")
     */
    public function livesearchAction(Request $request, $slug)
    {
        $query = $this->getDoctrine()->getRepository('AppBundle:Post')->livesearch($slug);
        return new Response($query);
    }
}
