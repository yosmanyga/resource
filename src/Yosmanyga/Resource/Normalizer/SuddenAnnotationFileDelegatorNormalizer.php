<?php

namespace Yosmanyga\Resource\Normalizer;

use Yosmanyga\Resource\Resource;

class SuddenAnnotationFileDelegatorNormalizer extends DelegatorNormalizer
{
    /**
     * {@inheritdoc}
     */
    public function supports($data, Resource $resource)
    {
        if ($resource->hasType('type') && 'annotation' == $resource->getType()) {
            return true;
        }

        return false;
    }
}
