<?php

namespace Yosmanyga\Resource\Transformer;

use Yosmanyga\Resource\Transformer\TransformerInterface;

class AbsoluteFileTransformer implements TransformerInterface
{
    /**
     * @inheritdoc
     */
    public function supports($resource, $parentResource)
    {
        if ($resource->hasMetadata('file') && 0 === strpos(parse_url($resource->getMetadata('file'), PHP_URL_PATH), '/')) {
            return true;
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function transform($resource, $parentResource)
    {
        return $resource;
    }
}
