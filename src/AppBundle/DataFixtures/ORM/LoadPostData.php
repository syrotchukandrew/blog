<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Post;
use AppBundle\Entity\Comment;
use Faker\Factory;

class LoadTeamData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();
        for ($i = 0; $i < 50; $i++) {
            static $id = 1;
            $post = new Post();
            $post->setTitle($faker->sentence);
            $post->setContent($faker->realText($maxNbChars = 3000, $indexSize = 2));
            $manager->persist($post);
            $this->addReference("{$id}", $post);
            $id = $id + 1;

            $rand = rand(3,7);
            for ($i = 0; $i < $rand; $i++) {
                $comment = new Comment();
                $comment -> setContent($faker->realText($maxNbChars = 500, $indexSize = 2));
                $comment-> setPost($post);
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