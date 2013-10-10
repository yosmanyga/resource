<?php

namespace Yosmanyga\Resource\Normalizer;

use Yosmanyga\Resource\ResourceInterface;

/**
 * Interface used by normalizers.
 */
interface NormalizerInterface
{
    /**
     * Returns whether normalizer supports the given data and resource.
     *
     * @param  mixed                                 $data
     * @param  \Yosmanyga\Resource\ResourceInterface $resource
     * @return boolean true if the normalizer supports the data and resource,
     *         false otherwise
     */
    public function supports($data, ResourceInterface $resource);

    /**
     * Normalizes and returns the data.
     *
     * @param  mixed                                 $data
     * @param  \Yosmanyga\Resource\ResourceInterface $resource
     * @return mixed
     */
    public function normalize($data, ResourceInterface $resource);
}
