<?php

namespace Yosmanyga\Resource\Compiler;

interface CompilerInterface
{
    public function supports($definition);

    public function compile($definition);
}
