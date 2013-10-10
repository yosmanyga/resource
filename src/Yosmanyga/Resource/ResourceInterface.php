<?php

namespace Yosmanyga\Resource;

/**
 * Interface used by resources.
 */
interface ResourceInterface
{
    /**
     * Returns the resource type.
     *
     * @return string
     */
    public function getType();

    /**
     * Returns whether the resource has type.
     *
     * @return bool true if the reader has type, false otherwise
     */
    public function hasType();

    /**
     * Gets the metadata of given key.
     * If $key is null, it returns all metadata.
     *
     * @param  string $key
     * @return mixed
     */
    public function getMetadata($key = '');

    /**
     * Returns whether the resource has a metadata of given key.
     *
     * @param  string $key
     * @return bool true if the resource has a metadata of given key, false
     * otherwise
     */
    public function hasMetadata($key);
}
