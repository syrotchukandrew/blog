<?php
namespace AppBundle\EventListener;

use AppBundle\Entity\Comment;
use AppBundle\Entity\Post;
use AppBundle\Entity\Tag;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use AppBundle\Utils\Slugger;
use Doctrine\ORM\Event\PreUpdateEventArgs;

class StofEventSubscriber implements EventSubscriber
{
    private $slugger;

    public function __construct(Slugger $slugger)
    {
        $this->slugger = $slugger;
    }

    public function getSubscribedEvents()
    {
        return array('prePersist', 'preUpdate');
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $now = new \DateTime();
        if ($entity instanceof Comment) {
            $entity->setCreated($now);
        }
        if ($entity instanceof Post || $entity instanceof Tag) {
            $entity->setCreated($now);
            $entity->setUpdated($now);
            $entity->setSlug($this->slugger->slugger($entity->getTitle()));

        }
    }

    public function preUpdate(PreUpdateEventArgs $eventArgs)
    {
        $entity = $eventArgs->getEntity();
        if ($entity instanceof Post || $entity instanceof Comment) {
            if ($eventArgs->hasChangedField('title') || $eventArgs->hasChangedField('content')) {
                $now = new \DateTime();
                $entity->setContentChanged($now);
            }
        }
    }
}
