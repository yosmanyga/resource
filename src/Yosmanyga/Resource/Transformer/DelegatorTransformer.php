<?php

namespace Yosmanyga\Resource\Transformer;

use Yosmanyga\Resource\Transformer\TransformerInterface;

class DelegatorTransformer implements TransformerInterface
{
    /**
     * @var \Yosmanyga\Resource\Transformer\TransformerInterface[]
     */
    private $transformers;

    /**
     * @param \Yosmanyga\Resource\Transformer\TransformerInterface[] $transformers
     */
    public function __construct($transformers = array())
    {
        $this->transformers = $transformers;
    }

    /**
     * @inheritdoc
     */
    public function supports($resource, $parentResource)
    {
        foreach ($this->transformers as $i => $transformer) {
            if ($transformer->supports($resource, $parentResource)) {
                if (0 != $i) {
                    // Move transformer to top to improve next pick
                    array_unshift($this->transformers, $transformer);
                    unset($this->transformers[$i + 1]);
                }

                return true;
            }
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function transform($resource, $parentResource)
    {
        return $this->pickTransformer($resource, $parentResource)->transform($resource, $parentResource);
    }

    /**
     * @param  \Yosmanyga\Resource\ResourceInterface $resource
     * @param  \Yosmanyga\Resource\ResourceInterface $parentResource
     * @throws \RuntimeException If no transformer is able to support the
     *         resource
     * @return \Yosmanyga\Resource\Transformer\TransformerInterface
     */
    private function pickTransformer($resource, $parentResource)
    {
        foreach ($this->transformers as $i => $transformer) {
            if ($transformer->supports($resource, $parentResource)) {
                if (0 != $i) {
                    // Move transformer to top to improve next pick
                    array_unshift($this->transformers, $transformer);
                    unset($this->transformers[$i + 1]);
                }

                return $transformer;
            }
        }

        throw new \RuntimeException('No transformer is able to support the resource.');
    }
}
