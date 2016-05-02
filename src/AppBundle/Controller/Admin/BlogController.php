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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;


/**
 * @Security("has_role('ROLE_ADMIN')")
 * @Route("/admin")
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
     * @Route("/admin/newpost", name="admin_post_new")
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
            $post->setAuthorEmail($this->getUser());
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
     * @Route("/{slug}", name="admin_post_show", options={"expose"=true})
     * @Method("GET")
     */
    public function showAction(Post $post)
    {
        $deleteForm = $this->createDeleteForm($post);

        return $this->render('admin/blog/show.html.twig', array(
            'post' => $post,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * @Route("/{slug}/edit", name="admin_post_edit")
     * @Method({"GET", "POST"})
     * @Security("is_granted('edit', post)")
     */
    public function editAction(Post $post, Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $editForm = $this->createForm(PostType::class, $post);
        $deleteForm = $this->createDeleteForm($post);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $post = $this->get('app.file_manager')->fileManager($post);
            $entityManager->flush();
            return $this->redirectToRoute('admin_post_edit', array('slug' => $post->getSlug()));
        }
        return $this->render('admin/blog/edit.html.twig', array(
            'post' => $post,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * @Route("/{slug}/delete", name="admin_post_delete")
     * @Method("DELETE")
     * @Security("is_granted('remove', post)")
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
            ->getForm();
    }

    /**
     * @Route("/users/treat_users", name="treat_users")
     * @Method("GET")
     */
    public function treatUsersAction(Request $request)
    {
        $users = $this->getDoctrine()->getRepository('AppBundle:User')->findAll();
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $users,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('admin/blog/treat_users.html.twig', array('pagination' => $pagination));
    }

    /**
     * @Route("/users/lock_user/{username}", name="lock_user")
     * @Method("GET")
     */
    public function lockUserAction(Request $request, $username)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->findOneBy(array('username' => $username));
        $user->setLocked(true);
        $entityManager->flush();
        return $this->redirectToRoute('treat_users');
    }

    /**
     * @Route("/users/unlock_user/{username}", name="unlock_user")
     * @Method("GET")
     */
    public function unlockUserAction(Request $request, $username)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->findOneBy(array('username' => $username));
        $user->setLocked(false);
        $entityManager->flush();
        return $this->redirectToRoute('treat_users');
    }

    /**
     * @Route("/users/moderator/{username}", name="moderator")
     * @Method("GET")
     */
    public function moderatorAction(Request $request, $username)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->findOneBy(array('username' => $username));
        $user->setRoles(array('ROLE_MODERATOR'));
        $entityManager->flush();
        return $this->redirectToRoute('treat_users');
    }

    /**
     * @Route("/users/nomoderator/{username}", name="no_moderator")
     * @Method("GET")
     */
    public function noModeratorAction(Request $request, $username)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->findOneBy(array('username' => $username));
        $roles = $user->getRoles();
        if (in_array("ROLE_MODERATOR", $roles)) {
            $key = array_search("ROLE_MODERATOR", $roles);
            $roles[$key] = 'ROLE_USER';
            $user->setRoles($roles);
            $entityManager->flush();
        }

        return $this->redirectToRoute('treat_users');
    }

    /**
     * @Route("/admin/livesearch", name="admin_livesearch", options={"expose"=true})
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
            return $this->render('admin/blog/index.html.twig', array('pagination' => $pagination));
        }
    }
}

