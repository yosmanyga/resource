<?php

namespace Yosmanyga\Resource\Cacher\Storer;

use Yosmanyga\Resource\Resource;

class FileStorer implements StorerInterface
{
    /**
     * @var string
     */
    protected $dir;

    /**
     * @param string $dir
     */
    public function __construct($dir = null)
    {
        $this->dir = $dir ?: sys_get_temp_dir();
    }

    /**
     * {@inheritdoc}
     */
    public function add($data, Resource $resource)
    {
        file_put_contents($this->getFilename($resource), serialize($data));
    }

    /**
     * {@inheritdoc}
     */
    public function has(Resource $resource)
    {
        $file = $this->getFilename($resource);

        return is_file($file);
    }

    /**
     * {@inheritdoc}
     */
    public function get(Resource $resource)
    {
        $file = $this->getFilename($resource);

        return unserialize(file_get_contents($file));
    }

    /**
     * @param \Yosmanyga\Resource\Resource $resource
     *
     * @return string
     */
    protected function getFilename(Resource $resource)
    {
        return sprintf(
            '%s/%s',
            $this->dir,
            md5(serialize($resource))
        );
    }
}
