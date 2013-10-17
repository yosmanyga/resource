<?php

namespace Yosmanyga\Resource\Cacher;

use Yosmanyga\Resource\Resource;

/**
 * Interface used by cachers.
 */
interface CacherInterface
{
    /**
     * Caches given data from given resource.
     *
     * @param  \Yosmanyga\Resource\Resource $resource
     * @param  mixed                                 $data
     * @return mixed
     */
    public function store($data, Resource $resource);

    /**
     * Gets cached data from given resource.
     *
     * @param  \Yosmanyga\Resource\Resource $resource
     * @return mixed
     */
    public function retrieve(Resource $resource);

    /**
     * Returns whether cacher has cached data for given resource.
     *
     * @param  \Yosmanyga\Resource\Resource $resource
     * @return mixed true if the cacher has the cached data for given resource,
     *         false otherwise
     */
    public function check(Resource $resource);
}
