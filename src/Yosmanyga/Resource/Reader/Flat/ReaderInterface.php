<?php

namespace Yosmanyga\Resource\Reader\Flat;

use Yosmanyga\Resource\ResourceInterface;

/**
 * Interface used by flat readers.
 */
interface ReaderInterface
{
    /**
     * Returns whether reader supports the given resource.
     *
     * @param  \Yosmanyga\Resource\ResourceInterface $resource
     * @return boolean true if the reader supports the resource, false otherwise
     */
    public function supports(ResourceInterface $resource);

    /**
     * Reads and returns the resource content.
     *
     * @param  \Yosmanyga\Resource\ResourceInterface $resource
     * @return string The resource content
     */
    public function read(ResourceInterface $resource);
}
