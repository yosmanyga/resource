<?php

namespace Yosmanyga\Resource\Transformer;

/**
 * Interface used by transformers.
 */
interface TransformerInterface
{
    /**
     * Returns whether transformer supports the given resource and parent
     * resource.
     *
     * @param  \Yosmanyga\Resource\ResourceInterface $resource
     * @param  \Yosmanyga\Resource\ResourceInterface $parentResource
     * @return boolean true if the reader supports the resource and parent
     *         resource, false otherwise
     */
    public function supports($resource, $parentResource);

    /**
     * Transforms the resource into another resource.
     *
     * @param  \Yosmanyga\Resource\ResourceInterface $resource
     * @param  \Yosmanyga\Resource\ResourceInterface $parentResource
     * @return \Yosmanyga\Resource\ResourceInterface The transformed resource
     */
    public function transform($resource, $parentResource);
}
