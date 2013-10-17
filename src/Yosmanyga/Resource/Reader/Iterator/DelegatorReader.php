<?php

namespace Yosmanyga\Resource\Reader\Iterator;

use Yosmanyga\Resource\ResourceInterface;

class DelegatorReader implements ReaderInterface
{
    /**
     * @var \Yosmanyga\Resource\Reader\Iterator\ReaderInterface[]
     */
    private $readers;

    /**
     * @var \Yosmanyga\Resource\Reader\Iterator\ReaderInterface
     */
    private $reader;

    /**
     * @param \Yosmanyga\Resource\Reader\Iterator\ReaderInterface[] $readers
     */
    public function __construct($readers = array())
    {
        $this->readers = $readers;
    }

    /**
     * @inheritdoc
     */
    public function supports(ResourceInterface $resource)
    {
        foreach ($this->readers as $i => $reader) {
            if ($reader->supports($resource)) {
                if (0 != $i) {
                    // Move reader to top to improve next pick
                    array_unshift($this->readers, $reader);
                    unset($this->readers[$i + 1]);
                }

                return true;
            }
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function open(ResourceInterface $resource)
    {
        $this->reader = $this->pickReader($resource);
        $this->reader->open($resource);
    }

    /**
     * @inheritdoc
     */
    public function current()
    {
        return $this->reader->current();
    }

    /**
     * @inheritdoc
     */
    public function next()
    {
        $this->reader->next();
    }

    /**
     * @inheritdoc
     */
    public function close()
    {
        $this->reader->close();
        unset($this->readers);
        unset($this->reader);
    }

    /**
     * @param  \Yosmanyga\Resource\ResourceInterface $resource
     * @throws \RuntimeException If no reader is able to support the resource
     * @return \Yosmanyga\Resource\Reader\Iterator\ReaderInterface
     */
    private function pickReader($resource)
    {
        foreach ($this->readers as $i => $reader) {
            if ($reader->supports($resource)) {
                if (0 != $i) {
                    // Move reader to top to improve next pick
                    array_unshift($this->readers, $reader);
                    unset($this->readers[$i + 1]);
                }

                return $reader;
            }
        }

        throw new \RuntimeException('No reader is able to support the resource.');
    }
}
