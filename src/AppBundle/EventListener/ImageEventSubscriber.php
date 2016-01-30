<?php
namespace AppBundle\EventListener;

use AppBundle\Entity\Post;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;

class ImageEventSubscriber implements EventSubscriber
{
    public function getSubscribedEvents()
    {
        return array(
            'preRemove',
            'preUpdate'
        );
    }

    public function preUpdate(PreUpdateEventArgs $eventArgs)
    {
        $post = $eventArgs->getEntity();
        if ($post instanceof Post) {
            if (null === $post->getImageName()) {
                return;
            }
            if (file_exists($eventArgs->getOldValue('imageName'))) {
                unlink($eventArgs->getOldValue('imageName'));
            }
        }
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function preRemove(LifecycleEventArgs $args)
    {
        $post = $args->getEntity();
        if ($post instanceof Post && $post->getImageName() !== null && file_exists($post->getImageName())) {
            unlink($post->getImageName());
        }
    }

}