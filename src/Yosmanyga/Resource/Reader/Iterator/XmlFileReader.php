<?php

namespace Yosmanyga\Resource\Reader\Iterator;

use Yosmanyga\Resource\Resource;
use Yosmanyga\Resource\Util\XmlKit;

class XmlFileReader implements ReaderInterface
{
    /**
     * @var \XMLReader
     */
    private $xmlReader;

    /**
     * @var \Yosmanyga\Resource\Util\XmlKit
     */
    private $xmlKit;

    public function __construct(XmlKit $xmlKit = null)
    {
        $this->xmlKit = $xmlKit ?: new XmlKit();
    }

    /**
     * {@inheritdoc}
     */
    public function supports(Resource $resource)
    {
        if ($resource->hasType()) {
            if ('xml' == $resource->getType()) {
                return true;
            }

            return false;
        }

        if ($resource->hasMetadata('file') && in_array(pathinfo($resource->getMetadata('file'), PATHINFO_EXTENSION), ['xml'])) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function open(Resource $resource)
    {
        $file = $resource->getMetadata('file');

        if (!is_file($file)) {
            throw new \InvalidArgumentException(sprintf('File "%s" not found.', $file));
        }

        $this->xmlReader = new \XmlReader();
        $this->xmlReader->open($file);

        // Try to move pointer to first node inside root tag
        try {
            while ($this->xmlReader->read()) {
                $name = $this->xmlReader->name;
                $depth = $this->xmlReader->depth;
                if (1 == $depth && !in_array($name, ['', '#text'])) {
                    break;
                }
            }
        } catch (\Exception $e) {
            throw new \InvalidArgumentException($e->getMessage(), 0, $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        if (!isset($this->xmlReader)) {
            throw new \RuntimeException('The resource needs to be open.');
        }

        if (!$this->xmlReader->name) {
            return false;
        }

        /** @var \DOMElement $data */
        $data = $this->xmlReader->expand();
        $data = $this->xmlKit->convertDomElementToArray($data);

        return ['value' => $data];
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        if (!isset($this->xmlReader)) {
            throw new \RuntimeException('The resource needs to be open.');
        }

        while ($this->xmlReader->next()) {
            $name = $this->xmlReader->name;
            $depth = $this->xmlReader->depth;
            if (1 == $depth && !in_array($name, ['', '#text'])) {
                break;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
        if (!isset($this->xmlReader)) {
            throw new \RuntimeException('The resource needs to be open.');
        }

        $this->xmlReader->close();
        unset($this->xmlReader);
    }
}
