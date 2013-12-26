<?php

namespace Yosmanyga\Resource\Cacher\Checker;

use Yosmanyga\Resource\Cacher\Storer\CheckFileStorer;
use Yosmanyga\Resource\Cacher\Storer\StorerInterface;
use Yosmanyga\Resource\Resource;

class SerializedDataChecker implements CheckerInterface
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
        if (!$resource->hasMetadata('data')) {
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
            serialize($resource->getMetadata('data')),
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

        return $this->storer->get($resource) == serialize($resource->getMetadata('data'));
    }
}
