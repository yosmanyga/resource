<?php

namespace Yosmanyga\Resource\Cacher\Storer;

use Yosmanyga\Resource\Resource;

/**
 * Interface used by storers.
 */
interface StorerInterface
{
    /**
     * Adds given data from given resource to the storer.
     *
     * @param mixed                        $data
     * @param \Yosmanyga\Resource\Resource $resource
     *
     * @return void
     */
    public function add($data, Resource $resource);

    /**
     * Returns whether storer has data for given resource.
     *
     * @param \Yosmanyga\Resource\Resource $resource
     *
     * @return bool true if the storer has the data for
     *              given resource, false otherwise
     */
    public function has(Resource $resource);

    /**
     * Gets data for given resource.
     *
     * @param \Yosmanyga\Resource\Resource $resource
     *
     * @return mixed The resource data
     */
    public function get(Resource $resource);
}
