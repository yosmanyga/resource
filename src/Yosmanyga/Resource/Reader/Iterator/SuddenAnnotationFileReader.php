<?php

namespace Yosmanyga\Resource\Reader\Iterator;

class SuddenAnnotationFileReader extends AnnotationFileReader
{
    protected function getData($file, $annotation)
    {
        return array(
            0 => $this->docParser->parse($file, $annotation)
        );
    }
}
