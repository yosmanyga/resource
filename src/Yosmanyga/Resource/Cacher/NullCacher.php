<?php

namespace Yosmanyga\Resource\Cacher;

use Yosmanyga\Resource\Cacher\CacherInterface;
use Yosmanyga\Resource\ResourceInterface;

class NullCacher implements CacherInterface
{
    /**
     * @inheritdoc
     */
    public function store($data, ResourceInterface $resource)
    {
    }

    /**
     * @inheritdoc
     */
    public function retrieve(ResourceInterface $resource)
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function check(ResourceInterface $resource)
    {
        return false;
    }
}
