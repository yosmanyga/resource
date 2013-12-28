<?php

namespace Yosmanyga\Resource\Reader\Iterator;

class SuddenAnnotationFileReader extends AnnotationFileReader
{
    protected function getData($file, $annotation)
    {
        $data = $this->docParser->parse($file, $annotation);

        if (!$data) {
            return array();
        }

        return array(
            0 => $data
        );
    }
}
