<?php

namespace Yosmanyga\Resource\Reader\Iterator;

use Yosmanyga\Resource\Resource;
use Symfony\Component\Finder\Finder;

class DirectoryReader implements ReaderInterface
{
    /**
     * @var \Yosmanyga\Resource\Reader\Iterator\DelegatorReader
     */
    private $delegatorReader;

    /**
     * @var \Symfony\Component\Finder\Finder
     */
    private $finder;

    /**
     * @var \Yosmanyga\Resource\Resource
     */
    private $resource;

    /**
     * @var \Iterator
     */
    private $iterator;

    /**
     * @param \Yosmanyga\Resource\Reader\Iterator\DelegatorReader $delegatorReader
     * @param \Symfony\Component\Finder\Finder $finder
     */
    public function __construct(DelegatorReader $delegatorReader, $finder = null)
    {
        $this->delegatorReader = $delegatorReader;
        $this->finder = $finder ?: new Finder();
    }

    /**
     * @inheritdoc
     */
    public function supports(Resource $resource)
    {
        if ($resource->hasType('type')) {
            if ('directory' == $resource->getType()) {
                return true;
            }

            return false;
        }

        if (!$resource->hasMetadata('dir') || !$resource->hasMetadata('type')) {
            return false;
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function open(Resource $resource)
    {
        $this->resource = $resource;
        $this->prepareFinder();

        $resource = $this->convertResource($resource, $this->iterator->current());
        $this->delegatorReader->open($resource);
    }

    /**
     * @inheritdoc
     */
    public function current()
    {
        return $this->delegatorReader->current();
    }

    /**
     * @inheritdoc
     */
    public function next()
    {
        $this->delegatorReader->next();
        if (!$this->delegatorReader->current()) {
            $this->iterator->next();
            if ($this->iterator->valid()) {
                $resource = $this->convertResource($this->resource, $this->iterator->current());
                $this->delegatorReader->open($resource);
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function close()
    {
        $this->delegatorReader->close();
    }

    private function prepareFinder()
    {
        if (!isset($this->resource)) {
            throw new \RuntimeException('Resource not set.');
        }

        $dir = $this->resource->getMetadata('dir');
        if (!is_dir($dir)) {
            throw new \InvalidArgumentException(sprintf('Directory "%s" not found.', $dir));
        }

        $this->finder->files()->in($dir);
        if ($this->resource->hasMetadata('filter')) {
            $this->finder->name($this->resource->getMetadata('filter'));
        }
        if ($this->resource->hasMetadata('depth')) {
            $this->finder->depth($this->resource->getMetadata('depth'));
        } else {
            $this->finder->depth('>= 0');
        }

        $this->iterator = $this->finder->getIterator();
        $this->iterator->rewind();
    }

    /**
     * @param \Yosmanyga\Resource\Resource $resource
     * @param \SplFileInfo $file
     * @return \Yosmanyga\Resource\Resource
     */
    private function convertResource(Resource $resource, \SplFileInfo $file)
    {
        return new Resource(
            array(
                'file' => $file->getRealpath()
            ),
            $resource->getMetadata('type')
        );
    }
}
