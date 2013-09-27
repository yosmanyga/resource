<?php

namespace Yosmanyga\Resource\Cacher\VersionChecker;

use Yosmanyga\Resource\ResourceInterface;
use Symfony\Component\Finder\Finder;

class DirectoryVersionChecker implements VersionCheckerInterface
{
    /**
     * @var \Symfony\Component\Finder\Finder
     */
    private $finder;

    /**
     * @param \Symfony\Component\Finder\Finder $finder
     */
    public function __construct($finder = null)
    {
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
    public function get(ResourceInterface $resource)
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

        $versions = array();
        /** @var \SplFileInfo $file */
        foreach ($this->finder->getIterator() as $file) {
            $versions[md5($file->getRealPath())] = filemtime($file);
        }

        return $versions;
    }
}
