<?php

namespace App\Listener;

use App\Entity\Property;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;


class ImageCacheSubscriber implements EventSubscriber
{

    /**
     * @var CacheManager
     */
    private $cacheManager;

    /**
     * @var ParameterBagInterface
     */
    private $params;

    public function __construct(CacheManager $cacheManager, ParameterBagInterface $params)
    {
        $this->cacheManager = $cacheManager;
        $this->params = $params;
    }

    public function getSubscribedEvents()
    {
        return [
            'preRemove',
            'preUpdate'
        ];
    }

    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if (!$entity instanceof Property) {
            return;
        }
        $images_directory = $this->params->get('images_directory');
        $this->cacheManager->remove($images_directory . '/' . $entity->getImages()->getName());
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();
        if (!$entity instanceof Property) {
            return;
        }
        if ($entity->getImages()->getName() instanceof UploadedFile) {
            $images_directory = $this->params->get('images_directory');
            $this->cacheManager->remove($images_directory . '/' . $entity->getImages()->getName());
        }
    }
}
