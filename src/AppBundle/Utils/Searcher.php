<?php
/**
 * Created by PhpStorm.
 * User: acer
 * Date: 27/01/16
 * Time: 13:12
 */

namespace AppBundle\Utils;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\HttpFoundation\RequestStack;

class Searcher //this service was created for practise - don't look for ratio
{
    private $doctrine;

    protected $requestStack;

    /**
     * Constructor.
     *
     * @param Registry $doctrine A Registry instance
     * @param RequestStack $doctrine A RequestStack instance
     */
    public function __construct(Registry $doctrine, RequestStack $requestStack)
    {
        $this->doctrine = $doctrine;
        $this->requestStack = $requestStack;
    }

    public function search()
    {
        $allPosts = $this->doctrine->getRepository('AppBundle:Post')->findAll();
        $request = $this->requestStack->getCurrentRequest();
        $slug = $request->get('slug');
        $method = $request->getMethod();
        $slugsTitles = array();
        $posts = array();
        foreach ($allPosts as $post) {
            $postTitle = $post->getTitle();
            if (stristr($postTitle, $slug)) {
                $postSlug = $post->getSlug();
                $slugsTitles[$postSlug] = $postTitle;
                $posts[] = $post;
            }
        }
        if ($method == 'GET') {
            return ($slugsTitles);
        } else {
            return ($posts);
        }
    }

}