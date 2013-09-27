<?php

namespace Yosmanyga\Resource\Reader\Flat;

use Yosmanyga\Resource\ResourceInterface;

class FileReader implements ReaderInterface
{
    /**
     * @inheritdoc
     */
    public function supports(ResourceInterface $resource)
    {
        if ($resource->hasType('type')) {
            if ('file' == $resource->getType()) {
                return true;
            }

            return false;
        }

        // Assumes that a resource with "file" metadata is an "file" resource
        if ($resource->hasMetadata('file')) {
            return true;
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function read(ResourceInterface $resource)
    {
        $file = $resource->getMetadata('file');

        if (!is_file($file)) {
            throw new \InvalidArgumentException(sprintf('File "%s" not found.', $file));
        }

        return file_get_contents($file);
    }
}
