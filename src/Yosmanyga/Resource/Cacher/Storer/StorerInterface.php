<?php

namespace Yosmanyga\Resource\Cacher\Storer;

use Yosmanyga\Resource\ResourceInterface;

/**
 * Interface used by storers.
 */
interface StorerInterface
{
    /**
     * Adds given data from given resource to the storer.
     *
     * @param  mixed                                 $data
     * @param  \Yosmanyga\Resource\ResourceInterface $resource
     * @return void
     */
    public function add($data, ResourceInterface $resource);

    /**
     * Returns whether storer has data for given resource.
     *
     * @param  \Yosmanyga\Resource\ResourceInterface $resource
     * @return boolean true if the storer has the data for given resource, false
     *         otherwise
     */
    public function has(ResourceInterface $resource);

    /**
     * Gets data for given resource.
     *
     * @param  \Yosmanyga\Resource\ResourceInterface $resource
     * @return mixed The resource data
     */
    public function get(ResourceInterface $resource);
}
