<?php

namespace Yosmanyga\Resource\Normalizer;

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
    public function supports($data, $resource);

    /**
     * Normalizes and returns the data.
     *
     * @param  mixed                                 $data
     * @param  \Yosmanyga\Resource\ResourceInterface $resource
     * @return mixed
     */
    public function normalize($data, $resource);
}
