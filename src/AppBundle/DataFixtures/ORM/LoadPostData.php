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

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();
        for ($i = 0; $i < 50; $i++) {
            static $id = 1;
            $post = new Post();
            $post->setTitle($faker->sentence);
            $post->setSlug($this->container->get('app.slugger')->slugger($post->getTitle()).$i);
            $post->setImageName("images/post/foto$id.jpg");
            $post->setShortText($faker->sentences(10,true));
            $post->setContent($faker->realText($maxNbChars = 5000, $indexSize = 2));
            $marks = array();
            for ($q = 0; $q < rand(1,10); $q++) {
                $marks[] = rand(1,5);
            }
            $post->setMarks($marks);
            $post->addMark(5);
            $manager->persist($post);
            $this->addReference("{$id}", $post);
            $id = $id + 1;

            $rand = rand(3, 7);
            for ($j = 0; $j < $rand; $j++) {
                $comment = new Comment();
                $comment->setContent($faker->realText($maxNbChars = 500, $indexSize = 2));
                $comment->setPost($post);
                $post->getComments()->add($comment);
                $manager->persist($comment);
                $manager->flush();
            }
        }
        $manager->flush();
    }

    public function getOrder()
    {
        return 1;
    }
}