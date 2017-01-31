<?php

namespace Yosmanyga\Resource\Normalizer;

use Yosmanyga\Resource\Resource;

class YamlFileDelegatorNormalizer extends DelegatorNormalizer
{
    /**
     * {@inheritdoc}
     */
    public function supports($data, Resource $resource)
    {
        if ($resource->hasType('type')) {
            if ('yaml' == $resource->getType()) {
                return true;
            }

            return false;
        }

        if ($resource->hasMetadata('file') && in_array(pathinfo($resource->getMetadata('file'), PATHINFO_EXTENSION), ['yaml', 'yml'])) {
            return true;
        }

        return false;
    }
}
