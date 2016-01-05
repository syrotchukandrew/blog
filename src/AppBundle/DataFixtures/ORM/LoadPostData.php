<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Post;
use AppBundle\Entity\Comment;
use Faker\Factory;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadPostData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    /** @var ContainerInterface */
    private $container;

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();
        for ($i = 0; $i < 50; $i++) {
            static $id = 1;
            $post = new Post();
            $post->setTitle($faker->sentence);
            $post->setContent($faker->realText($maxNbChars = 3000, $indexSize = 2));
            $post->setCreated(new \DateTime('now'));
            $post->setUpdated(new \DateTime('now'));
            $post->setSlug($post->getTitle());
            $manager->persist($post);
            $this->addReference("{$id}", $post);
            $id = $id + 1;

            $rand = rand(3,7);
            for ($j = 0; $j < $rand; $j++) {
                $comment = new Comment();
                $comment -> setContent($faker->realText($maxNbChars = 500, $indexSize = 2));
                $comment->setCreated(new \DateTime('now'));
                $comment-> setPost($post);
                $post->getComments()->add($comment);
                $manager->persist($comment);
                $manager->flush();
            }
        }



        $manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function getOrder()
    {
        return 1;
    }
}