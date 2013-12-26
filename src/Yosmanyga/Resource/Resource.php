<?php

namespace Yosmanyga\Resource;

class Resource
{
    /**
     * @var array
     */
    private $metadata;

    /**
     * @var string
     */
    private $type;

    /**
     * @param array  $metadata
     * @param string $type
     */
    public function __construct($metadata = array(), $type = '')
    {
        $this->metadata = $metadata;
        $this->type = $type;
    }

    /**
     * @param string $key
     * @param mixed  $value
     */
    public function setMetadata($key, $value)
    {
        $this->metadata[$key] = $value;
    }

    /**
     * @param  null|string  $key
     * @return string|array
     */
    public function getMetadata($key = null)
    {
        if (!$key) {
            return $this->metadata;
        }

        return $this->metadata[$key];
    }

    /**
     * @param  string  $key
     * @return boolean
     */
    public function hasMetadata($key)
    {
        return isset($this->metadata[$key]);
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return boolean
     */
    public function hasType()
    {
        return !empty($this->type);
    }
}
