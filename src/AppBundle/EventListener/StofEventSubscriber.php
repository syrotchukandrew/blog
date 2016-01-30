<?php
namespace AppBundle\EventListener;

use AppBundle\Entity\Comment;
use AppBundle\Entity\Post;
use AppBundle\Entity\Tag;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use AppBundle\Utils\Slugger;

class StofEventSubscriber implements EventSubscriber
{
    private $slugger;

    /**
     * Constructor.
     *
     */
    public function __construct( Slugger $slugger)
    {
        $this->slugger = $slugger;
    }
    public function getSubscribedEvents()
    {
        return array(
            'prePersist',
            'preUpdate'
        );
    }
    /**
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if($entity instanceof Comment) {
            $now = new \DateTime();
            $entity->setCreated($now);
        }
        if($entity instanceof Post) {
            $now = new \DateTime();
            $entity->setCreated($now);
            $entity->setUpdated($now);
            $entity->setSlug($this->slugger->slugger($entity->getTitle()));

        }
    }
    /**
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $args
     */
    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if($entity instanceof Post) {
            $now = new \DateTime();
            $entity->setUpdated($now);
        }
    }

}
