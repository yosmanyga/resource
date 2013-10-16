<?php

namespace Yosmanyga\Resource\Cacher\Checker;

use Yosmanyga\Resource\ResourceInterface;

/**
 * Interface used by checkers.
 */
interface CheckerInterface
{
    /**
     * Returns whether checker supports the given resource.
     *
     * @param  \Yosmanyga\Resource\ResourceInterface $resource
     * @return boolean true if the checker supports the resource, false
     *         otherwise
     */
    public function supports(ResourceInterface $resource);

    /**
     * Adds given resource into the checker.
     *
     * @param  \Yosmanyga\Resource\ResourceInterface $resource
     * @return void
     */
    public function add(ResourceInterface $resource);

    /**
     * Returns whether cache is valid for given resource.
     *
     * @param  \Yosmanyga\Resource\ResourceInterface $resource
     * @return boolean true if the cache is still valid, false
     *         otherwise
     */
    public function check(ResourceInterface $resource);
}
