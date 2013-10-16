<?php

namespace Yosmanyga\Resource\Cacher;

use Yosmanyga\Resource\Cacher\CacherInterface;
use Yosmanyga\Resource\Cacher\Checker\CheckerInterface;
use Yosmanyga\Resource\Cacher\Storer\StorerInterface;
use Yosmanyga\Resource\ResourceInterface;

class Cacher implements CacherInterface
{
    /**
     * @var \Yosmanyga\Resource\Cacher\Checker\CheckerInterface
     */
    private $checker;

    /**
     * @var \Yosmanyga\Resource\Cacher\Storer\StorerInterface
     */
    private $storer;

    /**
     * @param \Yosmanyga\Resource\Cacher\Storer\StorerInterface   $storer
     * @param \Yosmanyga\Resource\Cacher\Checker\CheckerInterface $checker
     */
    public function __construct(
        CheckerInterface $checker,
        StorerInterface $storer
    )
    {
        $this->checker = $checker;
        $this->storer = $storer;
    }

    /**
     * @inheritdoc
     */
    public function store($data, ResourceInterface $resource)
    {
        $this->checker->add($resource);
        $this->storer->add($data, $resource);
    }

    /**
     * @inheritdoc
     */
    public function retrieve(ResourceInterface $resource)
    {
        return $this->storer->get($resource);
    }

    /**
     * @inheritdoc
     */
    public function check(ResourceInterface $resource)
    {
        if ($this->checker->check($resource) && $this->storer->has($resource)) {
            return true;
        }

        return false;
    }

    public function __clone()
    {
        $this->checker = clone $this->checker;
        $this->storer = clone $this->storer;
    }
}
