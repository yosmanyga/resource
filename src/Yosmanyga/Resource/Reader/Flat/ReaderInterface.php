<?php

namespace Yosmanyga\Resource\Reader\Flat;

use Yosmanyga\Resource\Resource;

/**
 * Interface used by flat readers.
 */
interface ReaderInterface
{
    /**
     * Returns whether reader supports the given resource.
     *
     * @param  \Yosmanyga\Resource\Resource $resource
     * @return boolean true if the reader supports the resource, false otherwise
     */
    public function supports(Resource $resource);

    /**
     * Reads and returns the resource content.
     *
     * @param  \Yosmanyga\Resource\Resource $resource
     * @return string The resource content
     */
    public function read(Resource $resource);
}
