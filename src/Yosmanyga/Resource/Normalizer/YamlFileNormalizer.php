<?php

namespace Yosmanyga\Resource\Normalizer;

use Yosmanyga\Resource\Resource;

class YamlFileNormalizer implements NormalizerInterface
{
    /**
     * @var \Yosmanyga\Resource\Normalizer\DelegatorNormalizer
     */
    protected $normalizer;

    /**
     * @param $normalizers \Yosmanyga\Resource\Normalizer\NormalizerInterface[]
     */
    public function __construct($normalizers = array())
    {
        $this->normalizer = new DelegatorNormalizer($normalizers);
    }

    /**
     * @inheritdoc
     */
    public function supports($data, Resource $resource)
    {
        if ($resource->hasType('type')) {
            if ('yaml' == $resource->getType()) {
                return true;
            }

            return false;
        }

        if ($resource->hasMetadata('file') && in_array(pathinfo($resource->getMetadata('file'), PATHINFO_EXTENSION), array('yaml', 'yml'))) {
            return true;
        }

        return false;
    }

    /**
     * @param  mixed                        $data
     * @param  \Yosmanyga\Resource\Resource $resource
     * @return mixed
     */
    public function normalize($data, Resource $resource)
    {
        return $this->normalizer->normalize($data, $resource);
    }
}