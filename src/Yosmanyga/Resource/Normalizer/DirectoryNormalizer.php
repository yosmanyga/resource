<?php

namespace Yosmanyga\Resource\Normalizer;

use Yosmanyga\Resource\Normalizer\NormalizerInterface;
use Yosmanyga\Resource\Resource;
use Yosmanyga\Resource\ResourceInterface;

class DirectoryNormalizer implements NormalizerInterface
{
    /**
     * @var \Yosmanyga\Resource\Normalizer\DelegatorNormalizer
     */
    private $normalizer;

    /**
     * @param \Yosmanyga\Resource\Normalizer\NormalizerInterface[] $normalizers
     */
    public function __construct($normalizers = array())
    {
        $this->normalizer = new DelegatorNormalizer($normalizers);
    }

    /**
     * @inheritdoc
     */
    public function supports($data, $resource)
    {
        if (!$resource->hasMetadata('dir') || !$resource->hasMetadata('type')) {
            return false;
        }

        return $this->normalizer->supports($data, $this->convertResource($resource));
    }

    /**
     * @inheritdoc
     */
    public function normalize($data, $resource)
    {
        return $this->normalizer->normalize($data, $this->convertResource($resource));
    }

    /**
     * @param  \Yosmanyga\Resource\ResourceInterface $resource
     * @return \Yosmanyga\Resource\Resource
     */
    private function convertResource(ResourceInterface $resource)
    {
        return new Resource($resource->getMetadata(), $resource->getMetadata('type'));
    }
}
