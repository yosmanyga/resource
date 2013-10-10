<?php

namespace Yosmanyga\Resource\Util;

/**
 * Interface used by doc parser.
 */
interface DocParserInterface
{
    /**
     * Parses file and returns annotations of given name.
     * If $annotationName is null, it returns all annotations.
     *
     * @param $file
     * @param string|null $annotationName
     * @return array The array of annotations
     */
    public function parse($file, $annotationName = null);
}
