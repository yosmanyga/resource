<?php

namespace Yosmanyga\Resource\Transformer;

use Yosmanyga\Resource\Resource;

class RelativeFileTransformer implements TransformerInterface
{
    private $firstCharacters;

    public function __construct($firstCharacters = [])
    {
        $this->firstCharacters = $firstCharacters ?: ['@'];
    }

    /**
     * {@inheritdoc}
     */
    public function supports(Resource $resource, Resource $parentResource)
    {
        if (!$resource->hasMetadata('file') || 0 === strpos(parse_url($resource->getMetadata('file'), PHP_URL_PATH), '/')) {
            return false;
        }

        foreach ($this->firstCharacters as $character) {
            if (0 === strpos($resource->getMetadata('file'), $character)) {
                return false;
            }
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function transform(Resource $resource, Resource $parentResource)
    {
        $file = sprintf(
            '%s/%s',
            dirname($parentResource->getMetadata('file')),
            $resource->getMetadata('file')
        );

        return new Resource(['file' => $file]);
    }
}
