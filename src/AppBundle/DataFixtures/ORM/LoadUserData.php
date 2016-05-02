<?php


namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use AppBundle\Entity\User;

class LoadUserData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
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
        $passwordEncoder = $this->container->get('security.password_encoder');

        $user_user = new User();
        $user_user->setUsername('user_user');
        $user_user->setEmail('user_user@blog.com');
        $user_user->setRoles(array('ROLE_USER'));
        $user_user->setEnabled(true);
        $encodedPassword = $passwordEncoder->encodePassword($user_user, 'qweasz');
        $user_user->setPassword($encodedPassword);
        $manager->persist($user_user);

        $user_admin = new User();
        $user_admin->setUsername('user_admin');
        $user_admin->setEmail('user_admin@blog.com');
        $user_admin->setRoles(array('ROLE_ADMIN'));
        $user_admin->setEnabled(true);
        $encodedPassword = $passwordEncoder->encodePassword($user_admin, 'qweasz');
        $user_admin->setPassword($encodedPassword);
        $manager->persist($user_admin);

        $user_moderator = new User();
        $user_moderator->setUsername('user_moderator');
        $user_moderator->setEmail('user_moderator@blog.com');
        $user_moderator->setRoles(array('ROLE_MODERATOR'));
        $user_moderator->setEnabled(true);
        $encodedPassword = $passwordEncoder->encodePassword($user_moderator, 'qweasz');
        $user_moderator->setPassword($encodedPassword);
        $manager->persist($user_moderator);

        $manager->flush();
    }

    public function getOrder()
    {
        return 3;
    }
}