<?php

namespace AppBundle\Repository;

/**
 * TagRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TagRepository extends \Doctrine\ORM\EntityRepository
{
    public function getTags()
    {
        return $this->createQueryBuilder('tag')
            ->select('tag, post')
            ->leftJoin('tag.posts', 'post')
            ->getQuery()
            ->getResult();
    }

    public function getTagWithPosts($slug)
    {
        return $this->createQueryBuilder('tag')
            ->select('tag, post')
            ->where('tag.slug = :slug')
            ->leftJoin('tag.posts', 'post')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getResult();
    }
}
