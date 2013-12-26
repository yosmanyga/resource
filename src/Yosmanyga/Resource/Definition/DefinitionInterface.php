<?php

namespace Yosmanyga\Resource\Definition;

interface DefinitionInterface
{
    public function import($data);

    public function export();
}
