<?php

namespace Yosmanyga\Resource\Cacher\Checker;

use Yosmanyga\Resource\Resource;

/**
 * Interface used by checkers.
 */
interface CheckerInterface
{
    /**
     * Returns whether checker supports the given resource.
     *
     * @param  \Yosmanyga\Resource\Resource $resource
     * @return boolean true if the checker supports the resource, false
     *         otherwise
     */
    public function supports(Resource $resource);

    /**
     * Adds given resource into the checker.
     *
     * @param  \Yosmanyga\Resource\Resource $resource
     * @return void
     */
    public function add(Resource $resource);

    /**
     * Returns whether cache is valid for given resource.
     *
     * @param  \Yosmanyga\Resource\Resource $resource
     * @return boolean true if the cache is still valid, false
     *         otherwise
     */
    public function check(Resource $resource);
}
