<?php

namespace Yosmanyga\Resource\Reader\Iterator;

use Yosmanyga\Resource\Resource;

class IniFileReader implements ReaderInterface
{
    /**
     * @var \Yosmanyga\Resource\Reader\Iterator\InMemoryReader
     */
    private $inMemoryReader;

    /**
     * @inheritdoc
     */
    public function supports(Resource $resource)
    {
        if ($resource->hasType()) {
            if ('ini' == $resource->getType()) {
                return true;
            }

            return false;
        }

        if ($resource->hasMetadata('file') && in_array(pathinfo($resource->getMetadata('file'), PATHINFO_EXTENSION), array('ini'))) {
            return true;
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function open(Resource $resource)
    {
        $file = $resource->getMetadata('file');

        if (!is_file($file)) {
            throw new \InvalidArgumentException(sprintf('File "%s" not found.', $file));
        }

        try {
            $data = parse_ini_file($file, true);
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
}
