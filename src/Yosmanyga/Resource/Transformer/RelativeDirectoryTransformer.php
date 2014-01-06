<?php

namespace Yosmanyga\Resource\Transformer;

use Yosmanyga\Resource\Resource;

class RelativeDirectoryTransformer implements TransformerInterface
{
    private $firstCharacters;

    public function __construct($firstCharacters = array())
    {
        $this->firstCharacters = $firstCharacters ?: array('@');
    }

    /**
     * @inheritdoc
     */
    public function supports(Resource $resource, Resource $parentResource)
    {
        if (!$resource->hasMetadata('dir') || 0 === strpos(parse_url($resource->getMetadata('dir'), PHP_URL_PATH), '/')) {
            return false;
        }

        foreach ($this->firstCharacters as $character) {
            if (0 === strpos($resource->getMetadata('dir'), $character)) {
                return false;
            }
        }

        return true;
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
