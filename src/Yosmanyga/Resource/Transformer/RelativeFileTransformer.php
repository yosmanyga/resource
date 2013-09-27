<?php

namespace Yosmanyga\Resource\Transformer;

use Yosmanyga\Resource\Transformer\TransformerInterface;
use Yosmanyga\Resource\Resource;

class RelativeFileTransformer implements TransformerInterface
{
    /**
     * @inheritdoc
     */
    public function supports($resource, $parentResource)
    {
        if ($resource->hasMetadata('file') && 0 !== strpos(parse_url($resource->getMetadata('file'), PHP_URL_PATH), '/')) {
            return true;
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function transform($resource, $parentResource)
    {
        $file = sprintf(
            "%s/%s",
            dirname($parentResource->getMetadata('file')),
            $resource->getMetadata('file')
        );

        return new Resource(array('file' => $file));
    }
}
