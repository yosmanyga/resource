<?php

namespace Yosmanyga\Resource\Reader\Iterator;

use Yosmanyga\Resource\Resource;

/**
 * Interface used by iterator readers.
 */
interface ReaderInterface
{
    /**
     * Returns whether reader supports the given resource.
     *
     * @param  \Yosmanyga\Resource\Resource $resource
     * @return boolean                      true if the reader supports the resource, false otherwise
     */
    public function supports(Resource $resource);

    /**
     * Opens and prepares reader to be iterated.
     *
     * @param  \Yosmanyga\Resource\Resource $resource
     * @throws \InvalidArgumentException    If the resource has invalid metadata
     * @return void
     */
    public function open(Resource $resource);

    /**
     * Returns current item in the iteration.
     *
     * @return mixed The current item in the iteration
     */
    public function current();

    /**
     * Moves internal pointer to the next item in the iteration
     * If there are no more items, then the method "current" will returns false.
     *
     * @return void
     */
    public function next();

    /**
     * Closes reader.
     *
     * @return void
     */
    public function close();

}
