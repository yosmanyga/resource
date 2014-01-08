<?php

namespace Yosmanyga\Test\Resource\Normalizer;

use Yosmanyga\Resource\Normalizer\SuddenAnnotationFileDelegatorNormalizer;
use Yosmanyga\Resource\Resource;

class SuddenAnnotationFileDelegatorNormalizerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Yosmanyga\Resource\Normalizer\SuddenAnnotationFileDelegatorNormalizer::supports
     */
    public function testSupports()
    {
        $normalizer = new SuddenAnnotationFileDelegatorNormalizer();

        // Right type
        $this->assertTrue($normalizer->supports(null, new Resource(array(), 'annotation')));
        // Wrong type
        $this->assertFalse($normalizer->supports(null, new Resource(array(), 'foo')));
        // No type
        $this->assertFalse($normalizer->supports(null, new Resource(array())));
    }
}
