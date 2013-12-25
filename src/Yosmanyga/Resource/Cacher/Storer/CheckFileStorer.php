<?php

namespace Yosmanyga\Resource\Cacher\Storer;

use Yosmanyga\Resource\Resource;

class CheckFileStorer extends FileStorer
{
    /**
     * @var string
     */
    private $suffix;

    /**
     * @param string $dir
     * @param string $suffix
     */
    public function __construct($dir = null, $suffix = null)
    {
        parent::__construct($dir);

        $this->suffix = $suffix ?: '.check';
    }

    /**
     * @param \Yosmanyga\Resource\Resource $resource
     * @return string
     */
    protected function getFilename(Resource $resource)
    {
        return sprintf(
            "%s/%s%s",
            $this->dir,
            md5(serialize($resource)),
            $this->suffix
        );
    }
}
