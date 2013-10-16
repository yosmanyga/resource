<?php

namespace Yosmanyga\Resource\Cacher\Checker;

use Yosmanyga\Resource\ResourceInterface;
use Symfony\Component\Finder\Finder;

class DirectoryVersionChecker implements CheckerInterface
{
    /**
     * @var \Yosmanyga\Resource\Cacher\Storer\StorerInterface
     */
    private $storer;

    /**
     * @var \Symfony\Component\Finder\Finder
     */
    private $finder;

    /**
     * @param \Yosmanyga\Resource\Cacher\Storer\StorerInterface $storer
     * @param \Symfony\Component\Finder\Finder $finder
     */
    public function __construct($storer, $finder = null)
    {
        $this->storer = $storer;
        $this->finder = $finder ?: new Finder();
    }

    /**
     * @inheritdoc
     */
    public function supports(ResourceInterface $resource)
    {
        if (!$resource->hasMetadata('dir')) {
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
            $this->calculateDirVersion($resource),
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

        return $this->storer->get($resource) == $this->calculateDirVersion($resource);
    }

    private function calculateDirVersion(ResourceInterface $resource)
    {
        // Adjust finder
        $dir = $resource->getMetadata('dir');
        if (!is_dir($dir)) {
            throw new \InvalidArgumentException(sprintf('Directory "%s" not found.', $dir));
        }

        $this->finder->files()->in($dir);
        if ($resource->hasMetadata('filter')) {
            $this->finder->name($resource->getMetadata('filter'));
        }
        if ($resource->hasMetadata('depth')) {
            $this->finder->depth($resource->getMetadata('depth'));
        } else {
            $this->finder->depth('>= 0');
        }

        $version = array();
        /** @var \SplFileInfo $file */
        foreach ($this->finder->getIterator() as $file) {
            $version[md5($file->getRealPath())] = filemtime($file);
        }

        return $version;
    }
}
