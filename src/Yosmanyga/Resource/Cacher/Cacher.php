<?php

namespace Yosmanyga\Resource\Cacher;

use Yosmanyga\Resource\Cacher\Checker\CheckerInterface;
use Yosmanyga\Resource\Cacher\Checker\DelegatorChecker;
use Yosmanyga\Resource\Cacher\Storer\StorerInterface;
use Yosmanyga\Resource\Cacher\Storer\FileStorer;
use Yosmanyga\Resource\Resource;

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
    public function __construct(CheckerInterface $checker = null, StorerInterface $storer = null)
    {
        $this->checker = $checker ?: new DelegatorChecker();
        $this->storer = $storer ?: new FileStorer();
    }

    /**
     * @inheritdoc
     */
    public function store($data, Resource $resource)
    {
        $this->checker->add($resource);
        $this->storer->add($data, $resource);
    }

    /**
     * @inheritdoc
     */
    public function retrieve(Resource $resource)
    {
        return $this->storer->get($resource);
    }

    /**
     * @inheritdoc
     */
    public function check(Resource $resource)
    {
        if ($this->checker->check($resource) && $this->storer->has($resource)) {
            return true;
        }

        return false;
    }
}
