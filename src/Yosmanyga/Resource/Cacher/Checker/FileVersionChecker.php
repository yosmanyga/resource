<?php

namespace Yosmanyga\Resource\Cacher\Checker;

use Yosmanyga\Resource\Cacher\Storer\CheckFileStorer;
use Yosmanyga\Resource\Cacher\Storer\StorerInterface;
use Yosmanyga\Resource\Resource;

class FileVersionChecker implements CheckerInterface
{
    /**
     * @var \Yosmanyga\Resource\Cacher\Storer\StorerInterface
     */
    private $storer;

    /**
     * @param \Yosmanyga\Resource\Cacher\Storer\StorerInterface $storer
     */
    public function __construct(StorerInterface $storer = null)
    {
        $this->storer = $storer ?: new CheckFileStorer();
    }

    /**
     * @inheritdoc
     */
    public function supports(Resource $resource)
    {
        if (!$resource->hasMetadata('file')) {
            return false;
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function add(Resource $resource)
    {
        $this->storer->add(
            filemtime($resource->getMetadata('file')),
            $resource
        );
    }

    /**
     * @inheritdoc
     */
    public function check(Resource $resource)
    {
        if (!$this->storer->has($resource)) {
            return false;
        }

        return $this->storer->get($resource) == filemtime($resource->getMetadata('file'));
    }
}
