<?php

namespace Yosmanyga\Resource\Cacher\VersionChecker;

use Yosmanyga\Resource\ResourceInterface;

class DelegatorVersionChecker implements VersionCheckerInterface
{
    /**
     * @var \Yosmanyga\Resource\Cacher\VersionChecker\VersionCheckerInterface[]
     */
    private $versionCheckers;

    /**
     * @param \Yosmanyga\Resource\Cacher\VersionChecker\VersionCheckerInterface[] $versionCheckers
     */
    public function __construct($versionCheckers = array())
    {
        $this->versionCheckers = $versionCheckers;
    }

    /**
     * @inheritdoc
     */
    public function supports(ResourceInterface $resource)
    {
        foreach ($this->versionCheckers as $i => $versionCheckers) {
            if ($versionCheckers->supports($resource)) {
                if (0 != $i) {
                    // Move versionCheckers to top to improve next pick
                    array_unshift($this->versionCheckers, $versionCheckers);
                    unset($this->versionCheckers[$i + 1]);
                }

                return true;
            }
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function get(ResourceInterface $resource)
    {
        return $this->pickVersionChecker($resource)->get($resource);
    }

    /**
     * @param  \Yosmanyga\Resource\ResourceInterface $resource
     * @throws \RuntimeException If no versionChecker is able to support the
     *         resource
     * @return \Yosmanyga\Resource\Cacher\VersionChecker\VersionCheckerInterface
     */
    private function pickVersionChecker($resource)
    {
        foreach ($this->versionCheckers as $i => $versionCheckers) {
            if ($versionCheckers->supports($resource)) {
                if (0 != $i) {
                    // Move versionCheckers to top to improve next pick
                    array_unshift($this->versionCheckers, $versionCheckers);
                    unset($this->versionCheckers[$i + 1]);
                }

                return $versionCheckers;
            }
        }

        throw new \RuntimeException('No versionCheckers is able to support the resource.');
    }
}
