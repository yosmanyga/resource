<?php

namespace Yosmanyga\Resource\Compiler;

class DelegatorCompiler implements CompilerInterface
{
    /**
     * @var \Yosmanyga\Resource\Compiler\CompilerInterface[]
     */
    private $compilers;

    public function __construct($compilers = [])
    {
        $this->compilers = $compilers;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($definition)
    {
        try {
            if ($this->pickCompiler($definition)) {
                return true;
            }
        } catch (\RuntimeException $e) {
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function compile($definition)
    {
        return $this->pickCompiler($definition)->compile($definition);
    }

    /**
     * @param mixed $definition
     *
     * @throws \RuntimeException If no compiler is able to support the
     *                           resource
     *
     * @return \Yosmanyga\Resource\Compiler\CompilerInterface
     */
    protected function pickCompiler($definition)
    {
        foreach ($this->compilers as $i => $compiler) {
            if ($compiler->supports($definition)) {
                if (0 != $i) {
                    // Move compiler to top to improve next pick
                    unset($this->compilers[$i]);
                    array_unshift($this->compilers, $compiler);
                }

                return $this->compilers[0];
            }
        }

        throw new \RuntimeException('No compiler is able to work with the resource');
    }
}
