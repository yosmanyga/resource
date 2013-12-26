<?php

namespace Yosmanyga\Test\Resource\Normalizer;

use Yosmanyga\Resource\Normalizer\SuddenAnnotationFileNormalizer;
use Yosmanyga\Resource\Normalizer\DelegatorNormalizer;
use Yosmanyga\Resource\Resource;

class SuddenAnnotationFileNormalizerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Yosmanyga\Resource\Normalizer\SuddenAnnotationFileNormalizer::__construct
     */
    public function testConstructor()
    {
        $directoryNormalizer = new SuddenAnnotationFileNormalizer();
        $this->assertAttributeEquals(new DelegatorNormalizer(array()), 'normalizer', $directoryNormalizer);

        $internalNormalizer1 = $this->getMock('Yosmanyga\Resource\Normalizer\NormalizerInterface');
        $directoryNormalizer = new SuddenAnnotationFileNormalizer(array($internalNormalizer1));
        $this->assertAttributeEquals(new DelegatorNormalizer(array($internalNormalizer1)), 'normalizer', $directoryNormalizer);
    }

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

    /**
     * @covers Yosmanyga\Resource\Normalizer\SuddenAnnotationFileNormalizer::normalize
     */
    public function testNormalize()
    {
        $delegatorNormalizer = $this->getMock('Yosmanyga\Resource\Normalizer\DelegatorNormalizer');
        $normalizer = new SuddenAnnotationFileNormalizer();
        $p = new \ReflectionProperty($normalizer, 'normalizer');
        $p->setAccessible(true);
        $p->setValue($normalizer, $delegatorNormalizer);
        $delegatorNormalizer->expects($this->once())->method('normalize');
        $normalizer->normalize(null, new Resource());
    }
}
