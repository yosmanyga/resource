<?php

namespace Yosmanyga\Resource\Reader\Iterator;

use Yosmanyga\Resource\Resource;
use Yosmanyga\Resource\ResourceInterface;
use Yosmanyga\Resource\Util\DocParser;
use Yosmanyga\Resource\Util\DocParserInterface;

class AnnotationFileReader implements ReaderInterface
{
    /**
     * @var \Yosmanyga\Resource\Reader\Iterator\InMemoryReader
     */
    private $inMemoryReader;

    /**
     * @var \Yosmanyga\Resource\Util\DocParserInterface
     */
    private $docParser;

    /**
     * @param \Yosmanyga\Resource\Util\DocParserInterface $docParser
     */
    public function __construct(DocParserInterface $docParser = null)
    {
        $this->docParser = $docParser ?: new DocParser();
    }

    /**
     * @inheritdoc
     */
    public function supports(ResourceInterface $resource)
    {
        if ($resource->hasType('type') && 'annotation' == $resource->getType()) {
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
            $annotation = $resource->hasMetadata('annotation') ? $resource->getMetadata('annotation') : '';
            $data = $this->docParser->parse($file, $annotation);
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
        $this->inMemoryReader = clone $this->inMemoryReader;
        $this->docParser = clone $this->docParser;
    }
}
