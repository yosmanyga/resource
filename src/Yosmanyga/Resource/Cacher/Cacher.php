<?php

namespace Yosmanyga\Resource\Cacher;

use Yosmanyga\Resource\Cacher\CacherInterface;
use Yosmanyga\Resource\Cacher\VersionChecker\VersionCheckerInterface;
use Yosmanyga\Resource\Cacher\Storer\StorerInterface;
use Yosmanyga\Resource\ResourceInterface;

class Cacher implements CacherInterface
{
    /**
     * @var \Yosmanyga\Resource\Cacher\Storer\StorerInterface
     */
    private $dataStorer;

    /**
     * @var \Yosmanyga\Resource\Cacher\Storer\StorerInterface
     */
    private $versionStorer;

    /**
     * @var \Yosmanyga\Resource\Cacher\VersionChecker\VersionCheckerInterface
     */
    private $versionChecker;

    /**
     * @param \Yosmanyga\Resource\Cacher\Storer\StorerInterface                 $dataStorer
     * @param \Yosmanyga\Resource\Cacher\Storer\StorerInterface                 $versionStorer
     * @param \Yosmanyga\Resource\Cacher\VersionChecker\VersionCheckerInterface $versionChecker
     */
    public function __construct(
        StorerInterface $dataStorer,
        StorerInterface $versionStorer,
        VersionCheckerInterface $versionChecker
    )
    {
        $this->dataStorer = $dataStorer;
        $this->versionStorer = $versionStorer;
        $this->versionChecker = $versionChecker;
    }

    /**
     * @inheritdoc
     */
    public function store($data, ResourceInterface $resource)
    {
        $this->dataStorer->add($data, $resource);
        $this->versionStorer->add($this->versionChecker->get($resource), $resource);
    }

    /**
     * @inheritdoc
     */
    public function retrieve(ResourceInterface $resource)
    {
        return $this->dataStorer->get($resource);
    }

    /**
     * @inheritdoc
     */
    public function check(ResourceInterface $resource)
    {
        if (!$this->versionStorer->has($resource)) {
            return false;
        }

        if ($this->versionChecker->get($resource) != $this->versionStorer->get($resource)) {
            return false;
        }

        return true;
    }

    public function __clone()
    {
        $this->versionChecker = clone $this->versionChecker;
        $this->dataStorer = clone $this->dataStorer;
        $this->versionStorer = clone $this->versionStorer;
    }
}
