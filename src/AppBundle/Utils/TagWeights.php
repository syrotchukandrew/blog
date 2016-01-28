<?php
/**
 * Created by PhpStorm.
 * User: acer
 * Date: 27/01/16
 * Time: 16:36
 */

namespace AppBundle\Utils;

use Doctrine\Bundle\DoctrineBundle\Registry;


class TagWeights
{
    private $doctrine;

    /**
     * Constructor.
     *
     * @param Registry $doctrine A Registry instance
     */
    public function __construct(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function tagWeights()
    {
        $tags = $this->doctrine->getRepository('AppBundle:Tag')->getTags();
        $tagWeights = array();
        foreach ($tags as $tag) {
            $tagWeights[$tag->getTitle()] = count($tag->getPosts());
        }
        $max = max($tagWeights);
        $multiplier = ($max > 5) ? 5 / $max : 1;
        foreach ($tagWeights as &$tag)
        {
            $tag = ceil($tag * $multiplier);
        }
        return $tagWeights;
    }

}