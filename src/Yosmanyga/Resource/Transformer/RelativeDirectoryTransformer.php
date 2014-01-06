<?php

namespace Yosmanyga\Resource\Transformer;

use Yosmanyga\Resource\Resource;

class RelativeDirectoryTransformer implements TransformerInterface
{
    /**
     * @inheritdoc
     */
    public function supports(Resource $resource, Resource $parentResource)
    {
        if ($resource->hasMetadata('dir')
                && 0 !== strpos(parse_url($resource->getMetadata('dir'), PHP_URL_PATH), '/')
                && 0 !== strpos($resource->getMetadata('dir'), '@')) {
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
            $resource->getMetadata('dir')
        );

        return new Resource(array('file' => $file));
    }
}
