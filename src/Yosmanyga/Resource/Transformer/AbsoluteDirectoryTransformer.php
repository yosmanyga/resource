<?php

namespace Yosmanyga\Resource\Transformer;

use Yosmanyga\Resource\Transformer\TransformerInterface;
use Yosmanyga\Resource\Resource;

class AbsoluteDirectoryTransformer implements TransformerInterface
{
    /**
     * @inheritdoc
     */
    public function supports(Resource $resource, Resource $parentResource)
    {
        if ($resource->hasMetadata('dir') && 0 === strpos(parse_url($resource->getMetadata('dir'), PHP_URL_PATH), '/')) {
            return true;
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function transform(Resource $resource, Resource $parentResource)
    {
        return $resource;
    }
}
