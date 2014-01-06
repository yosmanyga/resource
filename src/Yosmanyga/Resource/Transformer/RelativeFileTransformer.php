<?php

namespace Yosmanyga\Resource\Transformer;

use Yosmanyga\Resource\Resource;

class RelativeFileTransformer implements TransformerInterface
{
    /**
     * @inheritdoc
     */
    public function supports(Resource $resource, Resource $parentResource)
    {
        if ($resource->hasMetadata('file')
                && 0 !== strpos(parse_url($resource->getMetadata('file'), PHP_URL_PATH), '/')
                && 0 !== strpos($resource->getMetadata('file'), '@')) {
            return true;
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function transform(Resource $resource, Resource $parentResource)
    {
        $file = sprintf(
            "%s/%s",
            dirname($parentResource->getMetadata('file')),
            $resource->getMetadata('file')
        );

        return new Resource(array('file' => $file));
    }
}
