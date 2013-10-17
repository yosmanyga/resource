<?php

namespace Yosmanyga\Resource\Cacher\Checker;

use Yosmanyga\Resource\ResourceInterface;

class DelegatorChecker implements CheckerInterface
{
    /**
     * @var \Yosmanyga\Resource\Cacher\Checker\CheckerInterface[]
     */
    private $checkers;

    /**
     * @param \Yosmanyga\Resource\Cacher\Checker\CheckerInterface[] $checkers
     */
    public function __construct($checkers = array())
    {
        $this->checkers = $checkers;
    }

    /**
     * @inheritdoc
     */
    public function supports(ResourceInterface $resource)
    {
        foreach ($this->checkers as $i => $checkers) {
            if ($checkers->supports($resource)) {
                if (0 != $i) {
                    // Move checkers to top to improve next pick
                    array_unshift($this->checkers, $checkers);
                    unset($this->checkers[$i + 1]);
                }

                return true;
            }
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function add(ResourceInterface $resource)
    {
        $this->pickChecker($resource)->add($resource);
    }

    /**
     * @inheritdoc
     */
    public function check(ResourceInterface $resource)
    {
        return $this->pickChecker($resource)->check($resource);
    }

    /**
     * @param  \Yosmanyga\Resource\ResourceInterface $resource
     * @throws \RuntimeException If no checker is able to support the
     *         resource
     * @return \Yosmanyga\Resource\Cacher\Checker\CheckerInterface
     */
    private function pickChecker($resource)
    {
        foreach ($this->checkers as $i => $checker) {
            if ($checker->supports($resource)) {
                if (0 != $i) {
                    // Move checkers to top to improve next pick
                    array_unshift($this->checkers, $checker);
                    unset($this->checkers[$i + 1]);
                }

                return $checker;
            }
        }

        throw new \RuntimeException('No checker is able to support the resource.');
    }
}
