<?php

namespace Yosmanyga\Resource\Reader\Iterator;

use Yosmanyga\Resource\ResourceInterface;

class InMemoryReader implements ReaderInterface
{
    /**
     * @var array
     */
    private $data;

    /**
     * @inheritdoc
     */
    public function supports(ResourceInterface $resource)
    {
        if ($resource->hasType('type')) {
            if ('in_memory' == $resource->getType()) {
                return true;
            }

            return false;
        }

        // Assumes that a resource with "data" metadata is an "in_memory" resource
        if ($resource->hasMetadata('data')) {
            return true;
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function open(ResourceInterface $resource)
    {
        $this->data = $resource->getMetadata('data');

        if (!is_array($this->data)) {
            throw new \InvalidArgumentException('Resource "data" metadata is not an array');
        }
    }

    /**
     * @inheritdoc
     */
    public function current()
    {
        if (!isset($this->data)) {
            throw new \RuntimeException('The resource needs to be open.');
        }

        $key = key($this->data);

        if (null === $key) {
            return false;
        }

        return array('key' => $key, 'value' => current($this->data));
    }

    /**
     * @inheritdoc
     */
    public function next()
    {
        if (!isset($this->data)) {
            throw new \RuntimeException('The resource needs to be open.');
        }

        next($this->data);
    }

    /**
     * @inheritdoc
     */
    public function close()
    {
        if (!isset($this->data)) {
            throw new \RuntimeException('The resource needs to be open.');
        }

        unset($this->data);
    }
}
