<?php

namespace Yosmanyga\Resource\Reader\Iterator;

use Yosmanyga\Resource\Resource;
use Yosmanyga\Resource\ResourceInterface;
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
     * @var \Yosmanyga\Resource\ResourceInterface
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
    public function supports(ResourceInterface $resource)
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
    public function open(ResourceInterface $resource)
    {
        $this->resource = $resource;
        $this->prepareFinder();
        $this->delegatorReader->open($this->createResource());
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
                $this->delegatorReader->open($this->createResource());
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

    private function createResource()
    {
        /** @var \SplFileInfo $file */
        $file = $this->iterator->current();

        return new Resource(
            array_merge(
                $this->resource->getMetadata(),
                array(
                    'file' => $file->getRealpath()
                )
            ),
            $this->resource->hasMetadata('type') ? $this->resource->getMetadata('type') : null
        );
    }

    public function __clone()
    {
        $this->delegatorReader = clone $this->delegatorReader;
        $this->finder = clone $this->finder;
        if (isset($this->resource)) {
            $this->resource = clone $this->resource;
        }
        if (isset($this->iterator)) {
            $this->iterator = clone $this->iterator;
        }
    }
}
