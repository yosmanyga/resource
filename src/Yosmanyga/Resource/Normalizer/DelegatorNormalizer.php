<?php

namespace Yosmanyga\Resource\Normalizer;

use Yosmanyga\Resource\Resource;

class DelegatorNormalizer implements NormalizerInterface
{
    /**
     * @var \Yosmanyga\Resource\Normalizer\NormalizerInterface[]
     */
    private $normalizers;

    /**
     * @param \Yosmanyga\Resource\Normalizer\NormalizerInterface[] $normalizers
     */
    public function __construct($normalizers = [])
    {
        $this->normalizers = $normalizers;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($data, Resource $resource)
    {
        try {
            if ($this->pickNormalizer($data, $resource)) {
                return true;
            }
        } catch (\RuntimeException $e) {
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function normalize($data, Resource $resource)
    {
        return $this->pickNormalizer($data, $resource)->normalize($data, $resource);
    }

    /**
     * @param mixed                        $data
     * @param \Yosmanyga\Resource\Resource $resource
     *
     * @throws \RuntimeException If no normalizer is able to support
     *                           the resource
     *
     * @return \Yosmanyga\Resource\Normalizer\NormalizerInterface
     */
    protected function pickNormalizer($data, Resource $resource)
    {
        foreach ($this->normalizers as $i => $normalizer) {
            if ($normalizer->supports($data, $resource)) {
                if (0 != $i) {
                    // Move normalizer to top to improve next pick
                    unset($this->normalizers[$i]);
                    array_unshift($this->normalizers, $normalizer);
                }

                return $this->normalizers[0];
            }
        }

        throw new \RuntimeException('No normalizer is able to work with the resource');
    }
}
