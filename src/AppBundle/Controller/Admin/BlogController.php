<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Form\PostType;
use AppBundle\Form\TagType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Post;
use AppBundle\Entity\Tag;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


/**
 * @Route("/admin/post")
 */
class BlogController extends Controller
{
    /**
     * @Route("/", name="admin_post_index")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $posts = $this->getDoctrine()->getRepository('AppBundle:Post')->getPostsWithTags();
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $posts,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('admin/blog/index.html.twig', array('pagination' => $pagination));
    }

    /**
     * @Route("/newpost", name="admin_post_new")
     * @Method({"GET", "POST"})
     */
    public function newPostAction(Request $request)
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post)
            ->add('saveAndCreateNew', SubmitType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($post);
            $entityManager->flush();

            if ($form->get('saveAndCreateNew')->isClicked()) {
                return $this->redirectToRoute('admin_post_new');
            }

            return $this->redirectToRoute('admin_post_index');
        }

        return $this->render('admin/blog/newpost.html.twig', array(
            'post' => $post,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/newtag", name="admin_tag_new")
     * @Method({"GET", "POST"})
     */
    public function newTagAction(Request $request)
    {
        $tag = new Tag();
        $form = $this->createForm(TagType::class, $tag)
            ->add('saveAndCreateNew', SubmitType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($tag);
            $entityManager->flush();

            if ($form->get('saveAndCreateNew')->isClicked()) {
                return $this->redirectToRoute('admin_tag_new');
            }

            return $this->redirectToRoute('admin_post_index');
        }

        return $this->render('admin/blog/newtag.html.twig', array(
            'tag' => $tag,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{slug}", name="admin_post_show")
     * @Method("GET")
     */
    public function showAction(Post $post)
    {
        $deleteForm = $this->createDeleteForm($post);

        return $this->render('admin/blog/show.html.twig', array(
            'post'        => $post,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * @Route("/{slug}/edit", name="admin_post_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Post $post, Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $editForm = $this->createForm(PostType::class, $post);
        $deleteForm = $this->createDeleteForm($post);

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('admin_post_edit', array('slug' => $post->getSlug()));
        }

        return $this->render('admin/blog/edit.html.twig', array(
            'post'        => $post,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * @Route("/{slug}/delete", name="admin_post_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Post $post)
    {
        $form = $this->createDeleteForm($post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->remove($post);
            $entityManager->flush();
        }
        return $this->redirectToRoute('admin_post_index');
    }

    private function createDeleteForm(Post $post)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_post_delete', array('slug' => $post->getSlug())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
