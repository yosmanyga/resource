<?php

namespace Yosmanyga\Resource\Normalizer;

use Yosmanyga\Resource\Normalizer\NormalizerInterface;
use Yosmanyga\Resource\ResourceInterface;

class DelegatorNormalizer implements NormalizerInterface
{
    /**
     * @var \Yosmanyga\Resource\Normalizer\NormalizerInterface[]
     */
    private $normalizers;

    /**
     * @param \Yosmanyga\Resource\Normalizer\NormalizerInterface[] $normalizers
     */
    public function __construct($normalizers = array())
    {
        $this->normalizers = $normalizers;
    }

    /**
     * @inheritdoc
     */
    public function supports($data, ResourceInterface $resource)
    {
        foreach ($this->normalizers as $i => $normalizer) {
            if ($normalizer->supports($data, $resource)) {
                if (0 != $i) {
                    // Move normalizer to top to improve next pick
                    array_unshift($this->normalizers, $normalizer);
                    unset($this->normalizers[$i + 1]);
                }

                return true;
            }
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function normalize($data, ResourceInterface $resource)
    {
        return $this->pickNormalizer($data, $resource)->normalize($data, $resource);
    }

    /**
     * @param  mixed                                 $data
     * @param  \Yosmanyga\Resource\ResourceInterface $resource
     * @throws \RuntimeException If no normalizer is able to support the
     *         resource
     * @return \Yosmanyga\Resource\Normalizer\NormalizerInterface
     */
    private function pickNormalizer($data, ResourceInterface $resource)
    {
        foreach ($this->normalizers as $i => $normalizer) {
            if ($normalizer->supports($data, $resource)) {
                if (0 != $i) {
                    // Move normalizer to top to improve next pick
                    array_unshift($this->normalizers, $normalizer);
                    unset($this->normalizers[$i + 1]);
                }

                return $this->normalizers[0];
            }
        }

        throw new \RuntimeException('No normalizer is able to work with the resource');
    }
}
