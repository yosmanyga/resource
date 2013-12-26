<?php

namespace Yosmanyga\Resource\Compiler;

class DelegatorCompiler implements CompilerInterface
{
    /**
     * @var \Yosmanyga\Resource\Compiler\CompilerInterface[]
     */
    private $compilers;

    public function __construct($compilers = array())
    {
        $this->compilers = $compilers;
    }

    /**
     * @inheritdoc
     */
    public function supports($definition)
    {
        foreach ($this->compilers as $i => $compiler) {
            if ($compiler->supports($definition)) {
                if (0 != $i) {
                    // Move compiler to top to improve next pick
                    array_unshift($this->compilers, $compiler);
                    unset($this->compilers[$i + 1]);
                }

                return true;
            }
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function compile($definition)
    {
        return $this->pickCompiler($definition)->compile($definition);
    }

    /**
     * @param  mixed                                          $definition
     * @throws \RuntimeException                              If no compiler is able to support the
     *                                                                   resource
     * @return \Yosmanyga\Resource\Compiler\CompilerInterface
     */
    private function pickCompiler($definition)
    {
        foreach ($this->compilers as $i => $compiler) {
            if ($compiler->supports($definition)) {
                if (0 != $i) {
                    // Move compiler to top to improve next pick
                    array_unshift($this->compilers, $compiler);
                    unset($this->compilers[$i + 1]);
                }

                return $this->compilers[0];
            }
        }

        throw new \RuntimeException('No compiler is able to work with the resource');
    }
}
