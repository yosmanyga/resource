<?php

namespace Yosmanyga\Resource\Reader\Flat;

use Yosmanyga\Resource\Resource;

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
        $this->readers = $readers ?: array(
            new FileReader()
        );
    }

    /**
     * @inheritdoc
     */
    public function supports(Resource $resource)
    {
        try {
            if ($this->pickReader($resource)) {
                return true;
            }
        } catch (\RuntimeException $e) {}

        return false;
    }

    /**
     * @inheritdoc
     */
    public function read(Resource $resource)
    {
        return $this->pickReader($resource)->read($resource);
    }

    /**
     * @param  \Yosmanyga\Resource\Resource $resource
     * @throws \RuntimeException            If no reader is able to support the
     *                                      resource
     * @return \Yosmanyga\Resource\Reader\Flat\ReaderInterface
     */
    protected function pickReader($resource)
    {
        foreach ($this->readers as $i => $reader) {
            if ($reader->supports($resource)) {
                if (0 != $i) {
                    // Move reader to top to improve next pick
                    unset($this->readers[$i]);
                    array_unshift($this->readers, $reader);
                }

                return $reader;
            }
        }

        throw new \RuntimeException('No reader is able to support the resource.');
    }
}
