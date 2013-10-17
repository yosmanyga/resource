<?php

namespace Yosmanyga\Resource\Cacher\Storer;

use Yosmanyga\Resource\Resource;

class FileStorer implements StorerInterface
{
    /**
     * @var string
     */
    private $dir;

    /**
     * @var string
     */
    private $suffix;

    /**
     * @param string $dir
     * @param string $suffix
     */
    public function __construct($dir, $suffix = '')
    {
        $this->dir = $dir;
        $this->suffix = $suffix;
    }

    /**
     * @inheritdoc
     */
    public function add($data, Resource $resource)
    {
        file_put_contents($this->getFilename($resource), serialize($data));
    }

    /**
     * @inheritdoc
     */
    public function has(Resource $resource)
    {
        $file = $this->getFilename($resource);

        return is_file($file);
    }

    /**
     * @inheritdoc
     */
    public function get(Resource $resource)
    {
        $file = $this->getFilename($resource);

        return unserialize(file_get_contents($file));
    }

    /**
     * @param \Yosmanyga\Resource\Resource $resource
     * @return string
     */
    private function getFilename(Resource $resource)
    {
        return sprintf(
            "%s/%s%s",
            $this->dir,
            md5(serialize($resource)),
            $this->suffix
        );
    }
}
