<?php

namespace Yosmanyga\Resource\Cacher\Checker;

use Yosmanyga\Resource\Cacher\Storer\StorerInterface;
use Yosmanyga\Resource\ResourceInterface;

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

    public function __construct(StorerInterface $storer, $ttl = 3600)
    {
        $this->storer = $storer;
        $this->ttl = $ttl;
    }

    /**
     * @inheritdoc
     */
    public function supports(ResourceInterface $resource)
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function add(ResourceInterface $resource)
    {
        $this->storer->add(
            time() + $this->ttl,
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

        return $this->storer->get($resource) >= time();
    }
}
