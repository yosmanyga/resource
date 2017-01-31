<?php

namespace Yosmanyga\Resource\Cacher;

use Yosmanyga\Resource\Resource;

class NullCacher implements CacherInterface
{
    /**
     * {@inheritdoc}
     */
    public function store($data, Resource $resource)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function retrieve(Resource $resource)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function check(Resource $resource)
    {
        return false;
    }
}
