<?php

namespace Yosmanyga\Resource\Reader\Flat;

use Yosmanyga\Resource\ResourceInterface;

class DelegatorReader implements ReaderInterface
{
    /**
     * @var \Yosmanyga\Resource\Reader\Flat\ReaderInterface[]
     */
    private $readers;

    /**
     * @param \Yosmanyga\Resource\Reader\Flat\ReaderInterface[] $readers
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
    public function read(ResourceInterface $resource)
    {
        return $this->pickReader($resource)->read($resource);
    }

    /**
     * @param  \Yosmanyga\Resource\ResourceInterface $resource
     * @throws \RuntimeException If no reader is able to support the resource
     * @return \Yosmanyga\Resource\Reader\Flat\ReaderInterface
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
