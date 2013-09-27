<?php

namespace Yosmanyga\Resource\Cacher;

use Yosmanyga\Resource\ResourceInterface;

/**
 * Interface used by cachers.
 */
interface CacherInterface
{
    /**
     * Cache given data from given resource.
     *
     * @param  \Yosmanyga\Resource\ResourceInterface $resource
     * @param  mixed                                 $data
     * @return mixed
     */
    public function store($data, ResourceInterface $resource);

    /**
     * Gets cached data from given resource.
     *
     * @param  \Yosmanyga\Resource\ResourceInterface $resource
     * @return mixed
     */
    public function retrieve(ResourceInterface $resource);

    /**
     * Returns whether cachers has cached data for given resource.
     *
     * @param  \Yosmanyga\Resource\ResourceInterface $resource
     * @return mixed true if the cacher has the cached data for given resource,
     *         false otherwise
     */
    public function check(ResourceInterface $resource);
}
