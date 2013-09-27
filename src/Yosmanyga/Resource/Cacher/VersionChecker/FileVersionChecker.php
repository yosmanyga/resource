<?php

namespace Yosmanyga\Resource\Cacher\VersionChecker;

use Yosmanyga\Resource\ResourceInterface;

class FileVersionChecker implements VersionCheckerInterface
{
    /**
     * @inheritdoc
     */
    public function supports(ResourceInterface $resource)
    {
        if (!$resource->hasMetadata('file')) {
            return false;
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function get(ResourceInterface $resource)
    {
        return filemtime($resource->getMetadata('file'));
    }
}
