<?php

namespace Yosmanyga\Resource\Cacher\Checker;

use Yosmanyga\Resource\Resource;

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
        $this->checkers = $checkers ?: array(
            new FileVersionChecker(),
            new DirectoryVersionChecker(),
            new SerializedDataChecker()
        );
    }

    /**
     * @inheritdoc
     */
    public function supports(Resource $resource)
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
    public function add(Resource $resource)
    {
        $this->pickChecker($resource)->add($resource);
    }

    /**
     * @inheritdoc
     */
    public function check(Resource $resource)
    {
        return $this->pickChecker($resource)->check($resource);
    }

    /**
     * @param  \Yosmanyga\Resource\Resource $resource
     * @throws \RuntimeException            If no checker is able to support the
     *                                      resource
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
