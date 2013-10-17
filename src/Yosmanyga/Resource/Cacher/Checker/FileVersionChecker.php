<?php

namespace Yosmanyga\Resource\Cacher\Checker;

use Yosmanyga\Resource\Cacher\Storer\StorerInterface;
use Yosmanyga\Resource\ResourceInterface;

class FileVersionChecker implements CheckerInterface
{
    /**
     * @var \Yosmanyga\Resource\Cacher\Storer\StorerInterface
     */
    private $storer;

    /**
     * @param \Yosmanyga\Resource\Cacher\Storer\StorerInterface $storer
     */
    public function __construct(StorerInterface $storer)
    {
        $this->storer = $storer;
    }

    /**
     * @inheritdoc
     */
    public function supports(ResourceInterface $resource)
    {
        if (!$resource->hasMetadata('file')) {
            return false;
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function add(ResourceInterface $resource)
    {
        $this->storer->add(
            filemtime($resource->getMetadata('file')),
            $resource
        );
    }

    /**
     * @inheritdoc
     */
    public function check(ResourceInterface $resource)
    {
        if (!$this->storer->has($resource)) {
            return false;
        }

        return $this->storer->get($resource) == filemtime($resource->getMetadata('file'));
    }
}