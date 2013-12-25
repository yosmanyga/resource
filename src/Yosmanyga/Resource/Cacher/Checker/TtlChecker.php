<?php

namespace Yosmanyga\Resource\Cacher\Checker;

use Yosmanyga\Resource\Cacher\Storer\FileStorer;
use Yosmanyga\Resource\Cacher\Storer\StorerInterface;
use Yosmanyga\Resource\Resource;

class TtlChecker implements CheckerInterface
{
    /**
     * @var \Yosmanyga\Resource\Cacher\Storer\StorerInterface
     */
    private $storer;

    /**
     * @var integer
     */
    private $ttl;

    public function __construct(StorerInterface $storer = null, $ttl = 3600)
    {
        $this->storer = $storer ?: new FileStorer();
        $this->ttl = $ttl;
    }

    /**
     * @inheritdoc
     */
    public function supports(Resource $resource)
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function add(Resource $resource)
    {
        $this->storer->add(
            time() + $this->ttl,
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

        return $this->storer->get($resource) >= time();
    }
}
