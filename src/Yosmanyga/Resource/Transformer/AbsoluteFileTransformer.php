<?php

namespace Yosmanyga\Resource\Transformer;

use Yosmanyga\Resource\Resource;

class AbsoluteFileTransformer implements TransformerInterface
{
    /**
     * {@inheritdoc}
     */
    public function supports(Resource $resource, Resource $parentResource)
    {
        if ($resource->hasMetadata('file') && 0 === strpos(parse_url($resource->getMetadata('file'), PHP_URL_PATH), '/')) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function transform(Resource $resource, Resource $parentResource)
    {
        return $resource;
    }
}
