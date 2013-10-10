<?php

namespace Yosmanyga\Resource\Transformer;

use Yosmanyga\Resource\Transformer\TransformerInterface;
use Yosmanyga\Resource\ResourceInterface;

class AbsoluteFileTransformer implements TransformerInterface
{
    /**
     * @inheritdoc
     */
    public function supports(ResourceInterface $resource, ResourceInterface $parentResource)
    {
        if ($resource->hasMetadata('file') && 0 === strpos(parse_url($resource->getMetadata('file'), PHP_URL_PATH), '/')) {
            return true;
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function transform(ResourceInterface $resource, ResourceInterface $parentResource)
    {
        return $resource;
    }
}
