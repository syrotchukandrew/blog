<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Post;
use AppBundle\Entity\Tag;
use AppBundle\Entity\Comment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\CommentType;
use AppBundle\Form\MarkType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use AppBundle\Form\PostType;


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
     * @Route("/posts/{slug}", name="blog_post", options={"expose"=true})
     */
    public function postShowAction(Post $post, Request $request)
    {
        $comments = $this->getDoctrine()->getRepository('AppBundle:Comment')->findBy(array('post' => $post));
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $comments,
            $request->query->getInt('page', 1),
            5
        );
        return $this->render('blog/post_show.html.twig', array('pagination' => $pagination, 'post' => $post));
    }

    /**
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @Route("/comment/{postSlug}/new", name = "comment_new")
     * @Method("POST")
     * @ParamConverter("post", options={"mapping": {"postSlug": "slug"}})
     */
    public function commentNewAction(Post $post, Request $request)
    {
        $comment = new Comment();
        $this->denyAccessUnlessGranted('create', $comment);
        $form = $this->createForm(CommentType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $comment = $form->getData();
            $comment->setPost($post);
            $comment->setAuthorEmail($this->getUser()->getEmail());
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
     * @Route("/comment/{slug}/{commentId}/edit", name="comment_edit")
     * @Method({"GET", "POST"})
     */
    public function editCommentAction(Request $request, $slug, $commentId)
    {
        $em = $this->getDoctrine()->getManager();
        $comment = $em->getRepository('AppBundle:Comment')->findOneBy(array('id' => $commentId));
        $this->denyAccessUnlessGranted('edit', $comment);
        $editForm = $this->createForm(CommentType::class, $comment);
        $editForm->add('submit', SubmitType::class,
            ['label' => 'Редагувати',
                'attr' => ['class' => 'btn btn-default left',
                    'type' => 'submit']
            ]
        );
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();
            return $this->redirectToRoute('blog_post', array('slug' => $slug));
        }
        return $this->render('blog/comment_edit.html.twig', array(
            'comment' => $comment,
            'edit_form' => $editForm->createView(),
        ));
    }

    /**
     * @Route("/comment/{slug}/{commentId}/delete", name = "comment_delete")
     * @Method({"GET", "DELETE"})
     */
    public function commentDeleteAction(Request $request, $commentId, $slug)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AppBundle:Comment')->findOneBy(array('id' => $commentId));
        $this->denyAccessUnlessGranted('remove', $entity);
        $form = $this->createForm(CommentType::class, $entity, [
            'method' => 'DELETE',
        ]);
        $form->add('submit', SubmitType::class,
            ['label' => 'Видалити',
                'attr' => ['class' => 'btn btn-default left',
                    'type' => 'submit']
            ]
        );

        if ($request->getMethod() == 'DELETE') {
            $form->handleRequest($request);
            if ($form->isValid() && $form->isSubmitted()) {
                $em->remove($entity);
                $em->flush();
                return $this->redirectToRoute('blog_post', array('slug' => $slug));
            }
        }
        return $this->render(':blog:comment_delete.html.twig', array(
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

    /**
     * @Route("/newpost", name="blog_post_new")
     * @Method({"GET", "POST"})
     */
    public function newPostAction(Request $request)
    {
        $post = new Post();
        $this->denyAccessUnlessGranted('create', $post);
        $form = $this->createForm(PostType::class, $post)
            ->add('saveAndCreateNew', SubmitType::class, array(
                'label' => 'Зберегти та створити ще',
                'attr' => [
                    'class' => 'btn btn-primary',
                ]
            ));
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $post = $this->get('app.file_manager')->fileManager($post);
            $email = $this->getUser()->getEmail();
            $post->setAuthorEmail($email);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($post);
            $entityManager->flush();
            if ($form->get('saveAndCreateNew')->isClicked()) {
                return $this->redirectToRoute('admin_post_new');
            }
            return $this->redirectToRoute('homepage');
        }
        return $this->render('blog/newpost.html.twig', array(
            'post' => $post,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/blog/{slug}/edit", name="blog_post_edit")
     * @Method({"GET", "POST"})
     * @Security("is_granted('edit', post)")
     */
    public function editPostAction(Post $post, Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $editForm = $this->createForm(PostType::class, $post);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $post = $this->get('app.file_manager')->fileManager($post);
            $entityManager->flush();
            return $this->redirectToRoute('blog_post', array('slug' => $post->getSlug()));
        }
        return $this->render('blog/edit.html.twig', array(
            'post' => $post,
            'edit_form' => $editForm->createView(),
        ));
    }

    /**
     * @Route("/post/{slug}/delete", name = "blog_post_delete")
     * @Method({"GET", "DELETE"})
     * @Security("is_granted('remove', post)")
     */
    public function postDeleteAction(Request $request, $slug, Post $post)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AppBundle:Post')->findOneBy(array('slug' => $slug));
        $form = $this->createForm(PostType::class, $entity, [
            'method' => 'DELETE',
        ]);
        $form->add('submit', SubmitType::class,
            ['label' => 'Видалити',
                'attr' => ['class' => 'btn btn-default left',
                    'type' => 'submit']
            ]
        );

        if ($request->getMethod() == 'DELETE') {
            $form->handleRequest($request);
            if ($form->isValid() && $form->isSubmitted()) {
                $em->remove($entity);
                $em->flush();
                return $this->redirectToRoute('homepage');
            }
        }
        return $this->render(':blog:post_delete.html.twig', array(
            'form' => $form->createView(),
        ));

    }


}
