<?php

namespace Yosmanyga\Resource\Normalizer;

use Yosmanyga\Resource\Resource;

class DirectoryNormalizer extends DelegatorNormalizer
{
    /**
     * @inheritdoc
     */
    public function supports($data, Resource $resource)
    {
        if (!$resource->hasMetadata('dir') || !$resource->hasMetadata('type')) {
            return false;
        }

        return parent::supports($data, $this->convertResource($resource));
    }

    /**
     * @inheritdoc
     */
    public function normalize($data, Resource $resource)
    {
        return parent::normalize($data, $this->convertResource($resource));
    }

    /**
     * @param  \Yosmanyga\Resource\Resource $resource
     * @return \Yosmanyga\Resource\Resource
     */
    private function convertResource(Resource $resource)
    {
        return new Resource($resource->getMetadata(), $resource->getMetadata('type'));
    }
}
