<?php

namespace Yosmanyga\Resource\Loader;

/**
 * Interface used by loaders.
 */
interface LoaderInterface
{
    /**
     * Loads the resource and returns the content.
     *
     * @param  \Yosmanyga\Resource\Resource $resource
     * @return mixed The resource content
     */
    public function load($resource);
}
