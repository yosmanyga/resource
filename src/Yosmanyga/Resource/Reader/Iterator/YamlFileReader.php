<?php

namespace Yosmanyga\Resource\Reader\Iterator;

use Yosmanyga\Resource\Resource;
use Yosmanyga\Resource\ResourceInterface;
use Symfony\Component\Yaml\Yaml;

class YamlFileReader implements ReaderInterface
{
    /**
     * @var \Yosmanyga\Resource\Reader\Iterator\InMemoryReader
     */
    private $inMemoryReader;

    /**
     * @inheritdoc
     */
    public function supports(ResourceInterface $resource)
    {
        if ($resource->hasType('type')) {
            if ('yaml' == $resource->getType()) {
                return true;
            }

            return false;
        }

        if ($resource->hasMetadata('file') && in_array(pathinfo($resource->getMetadata('file'), PATHINFO_EXTENSION), array('yaml', 'yml'))) {
            return true;
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function open(ResourceInterface $resource)
    {
        $file = $resource->getMetadata('file');

        if (!is_file($file)) {
            throw new \InvalidArgumentException(sprintf('File "%s" not found.', $file));
        }

        try {
            $data = (array) Yaml::parse($file);
        } catch (\Exception $e) {
            throw new \InvalidArgumentException($e->getMessage(), 0, $e);
        }

        $this->inMemoryReader = new InMemoryReader();
        $this->inMemoryReader->open(new Resource(array('data' => $data), 'in_memory'));
    }

    /**
     * @inheritdoc
     */
    public function current()
    {
        if (!isset($this->inMemoryReader)) {
            throw new \RuntimeException('The resource needs to be open.');
        }

        return $this->inMemoryReader->current();
    }

    /**
     * @inheritdoc
     */
    public function next()
    {
        if (!isset($this->inMemoryReader)) {
            throw new \RuntimeException('The resource needs to be open.');
        }

        $this->inMemoryReader->next();
    }

    /**
     * @inheritdoc
     */
    public function close()
    {
        if (!isset($this->inMemoryReader)) {
            throw new \RuntimeException('The resource needs to be open.');
        }

        $this->inMemoryReader->close();
        unset($this->inMemoryReader);
    }

    public function __clone()
    {
        if (isset($this->inMemoryReader)) {
            $this->inMemoryReader = clone $this->inMemoryReader;
        }
    }
}
