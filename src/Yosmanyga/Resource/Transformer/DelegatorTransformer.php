<?php

namespace Yosmanyga\Resource\Transformer;

use Yosmanyga\Resource\Resource;

class DelegatorTransformer implements TransformerInterface
{
    /**
     * @var \Yosmanyga\Resource\Transformer\TransformerInterface[]
     */
    private $transformers;

    /**
     * @param \Yosmanyga\Resource\Transformer\TransformerInterface[] $transformers
     */
    public function __construct($transformers = [])
    {
        $this->transformers = $transformers ?: [
            new RelativeFileTransformer(),
            new RelativeDirectoryTransformer(),
            new AbsoluteFileTransformer(),
            new AbsoluteDirectoryTransformer(),
            new ComposerVendorFileTransformer(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function supports(Resource $resource, Resource $parentResource)
    {
        try {
            if ($this->pickTransformer($resource, $parentResource)) {
                return true;
            }
        } catch (\RuntimeException $e) {
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function transform(Resource $resource, Resource $parentResource)
    {
        return $this->pickTransformer($resource, $parentResource)->transform($resource, $parentResource);
    }

    /**
     * @param \Yosmanyga\Resource\Resource $resource
     * @param \Yosmanyga\Resource\Resource $parentResource
     *
     * @throws \RuntimeException If no transformer is able to support
     *                           the resource
     *
     * @return \Yosmanyga\Resource\Transformer\TransformerInterface
     */
    protected function pickTransformer(Resource $resource, Resource $parentResource)
    {
        foreach ($this->transformers as $i => $transformer) {
            if ($transformer->supports($resource, $parentResource)) {
                if (0 != $i) {
                    // Move transformer to top to improve next pick
                    unset($this->transformers[$i]);
                    array_unshift($this->transformers, $transformer);
                }

                return $transformer;
            }
        }

        throw new \RuntimeException('No transformer is able to support the resource.');
    }
}
