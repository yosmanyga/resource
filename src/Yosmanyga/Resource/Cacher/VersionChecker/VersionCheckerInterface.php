<?php

namespace Yosmanyga\Resource\Cacher\VersionChecker;

use Yosmanyga\Resource\ResourceInterface;

/**
 * Interface used by version checkers.
 */
interface VersionCheckerInterface
{
    /**
     * Returns whether version checker supports the given resource.
     *
     * @param  \Yosmanyga\Resource\ResourceInterface $resource
     * @return boolean true if the version checker supports the resource, false
     *         otherwise
     */
    public function supports(ResourceInterface $resource);

    /**
     * Gets the version for given resource.
     *
     * @param  \Yosmanyga\Resource\ResourceInterface $resource
     * @return mixed
     */
    public function get(ResourceInterface $resource);
}
