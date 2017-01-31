<?php

namespace Yosmanyga\Resource\Normalizer;

use Yosmanyga\Resource\Resource;

/**
 * Interface used by normalizers.
 */
interface NormalizerInterface
{
    /**
     * Returns whether normalizer supports the given data and resource.
     *
     * @param mixed                        $data
     * @param \Yosmanyga\Resource\Resource $resource
     *
     * @return bool true if the normalizer supports the
     *              data and resource, false otherwise
     */
    public function supports($data, Resource $resource);

    /**
     * Normalizes and returns the data.
     *
     * @param mixed                        $data
     * @param \Yosmanyga\Resource\Resource $resource
     *
     * @return mixed
     */
    public function normalize($data, Resource $resource);
}
