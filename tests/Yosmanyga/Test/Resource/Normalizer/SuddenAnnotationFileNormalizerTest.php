<?php

namespace Yosmanyga\Test\Resource\Normalizer;

use Yosmanyga\Resource\Normalizer\SuddenAnnotationFileNormalizer;
use Yosmanyga\Resource\Normalizer\DelegatorNormalizer;
use Yosmanyga\Resource\Resource;

class SuddenAnnotationFileNormalizerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Yosmanyga\Resource\Normalizer\SuddenAnnotationFileNormalizer::supports
     */
    public function testSupports()
    {
        $normalizer = new SuddenAnnotationFileNormalizer();

        // Right type
        $this->assertTrue($normalizer->supports(null, new Resource(array(), 'annotation')));
        // Wrong type
        $this->assertFalse($normalizer->supports(null, new Resource(array(), 'foo')));
        // No type
        $this->assertFalse($normalizer->supports(null, new Resource(array())));
    }
}
