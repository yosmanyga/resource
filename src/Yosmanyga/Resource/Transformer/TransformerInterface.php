<?php

namespace Yosmanyga\Resource\Transformer;

use Yosmanyga\Resource\Resource;

/**
 * Interface used by transformers.
 */
interface TransformerInterface
{
    /**
     * Returns whether transformer supports the given resource and parent
     * resource.
     *
     * @param \Yosmanyga\Resource\Resource $resource
     * @param \Yosmanyga\Resource\Resource $parentResource
     *
     * @return bool true if the reader supports the
     *              resource and parent resource,
     *              false otherwise
     */
    public function supports(Resource $resource, Resource $parentResource);

    /**
     * Transforms the resource into another resource.
     *
     * @param \Yosmanyga\Resource\Resource $resource
     * @param \Yosmanyga\Resource\Resource $parentResource
     *
     * @return \Yosmanyga\Resource\Resource The transformed resource
     */
    public function transform(Resource $resource, Resource $parentResource);
}
