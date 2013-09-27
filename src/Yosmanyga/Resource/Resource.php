<?php

namespace Yosmanyga\Resource;

class Resource implements ResourceInterface
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
     * @inheritdoc
     */
    public function getMetadata($key = null)
    {
        if (!$key) {
            return $this->metadata;
        }

        return $this->metadata[$key];
    }

    /**
     * @inheritdoc
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
     * @inheritdoc
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @inheritdoc
     */
    public function hasType()
    {
        return !empty($this->type);
    }
}
