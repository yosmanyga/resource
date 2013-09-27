<?php

namespace Yosmanyga\Resource\Cacher\Storer;

use Yosmanyga\Resource\ResourceInterface;

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
    public function add($data, ResourceInterface $resource)
    {
        return file_put_contents($this->getFilename($resource), serialize($data));
    }

    /**
     * @inheritdoc
     */
    public function has(ResourceInterface $resource)
    {
        $file = $this->getFilename($resource);

        return is_file($file);
    }

    /**
     * @inheritdoc
     */
    public function get(ResourceInterface $resource)
    {
        $file = $this->getFilename($resource);

        return unserialize(file_get_contents($file));
    }

    /**
     * @param \Yosmanyga\Resource\ResourceInterface $resource
     * @return string
     */
    private function getFilename(ResourceInterface $resource)
    {
        return sprintf(
            "%s/%s%s",
            $this->dir,
            md5(serialize($resource)),
            $this->suffix
        );
    }
}
