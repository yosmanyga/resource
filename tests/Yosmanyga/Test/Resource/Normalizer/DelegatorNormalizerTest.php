<?php

namespace Yosmanyga\Test\Resource\Normalizer;

use Yosmanyga\Resource\Normalizer\DelegatorNormalizer;
use Yosmanyga\Resource\Resource;

class DelegatorNormalizerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Yosmanyga\Resource\Normalizer\DelegatorNormalizer::__construct
     */
    public function testConstructor()
    {
        $delegatorNormalizer = new DelegatorNormalizer();
        $this->assertAttributeEquals(array(), 'normalizers', $delegatorNormalizer);

        $internalNormalizer1 = $this->getMock('Yosmanyga\Resource\Normalizer\NormalizerInterface');
        $delegatorNormalizer = new DelegatorNormalizer(array($internalNormalizer1));
        $this->assertAttributeEquals(array($internalNormalizer1), 'normalizers', $delegatorNormalizer);
    }

    /**
     * @covers Yosmanyga\Resource\Normalizer\DelegatorNormalizer::supports
     */
    public function testSupports()
    {
        $internalNormalizer1 = $this->getMock('Yosmanyga\Resource\Normalizer\NormalizerInterface');
        $internalNormalizer2 = $this->getMock('Yosmanyga\Resource\Normalizer\NormalizerInterface');
        $internalNormalizer3 = $this->getMock('Yosmanyga\Resource\Normalizer\NormalizerInterface');
        $internalNormalizer2->expects($this->once())->method('supports')->will($this->returnValue(true));
        $normalizer = new DelegatorNormalizer(array($internalNormalizer1, $internalNormalizer2, $internalNormalizer3));
        $this->assertTrue($normalizer->supports('', new Resource()));
        $this->assertAttributeEquals(array(0 => $internalNormalizer2, 1 => $internalNormalizer1, 2 => $internalNormalizer3), 'normalizers', $normalizer);

        $internalNormalizer1 = $this->getMock('Yosmanyga\Resource\Normalizer\NormalizerInterface');
        $internalNormalizer1->expects($this->once())->method('supports')->will($this->returnValue(false));
        $normalizer = new DelegatorNormalizer(array($internalNormalizer1));
        $this->assertFalse($normalizer->supports('', new Resource()));
    }

    /**
     * @covers Yosmanyga\Resource\Normalizer\DelegatorNormalizer::normalize
     */
    public function testNormalize()
    {
        $data = array();
        $resource = new Resource();
        $internalNormalizer1 = $this->getMock('Yosmanyga\Resource\Normalizer\NormalizerInterface');
        $internalNormalizer1->expects($this->once())->method('supports')->will($this->returnValue(true));
        $internalNormalizer1->expects($this->once())->method('normalize')->with($data, $resource);
        $delegatorNormalizer = new DelegatorNormalizer(array($internalNormalizer1));
        $delegatorNormalizer->normalize($data, $resource);
    }

    /**
     * @covers Yosmanyga\Resource\Normalizer\DelegatorNormalizer::pickNormalizer
     */
    public function testPickNormalizer()
    {
        $r = new \ReflectionClass('Yosmanyga\Resource\Normalizer\DelegatorNormalizer');
        $m = $r->getMethod('pickNormalizer');
        $m->setAccessible(true);
        $internalNormalizer1 = $this->getMock('Yosmanyga\Resource\Normalizer\NormalizerInterface');
        $internalNormalizer2 = $this->getMock('Yosmanyga\Resource\Normalizer\NormalizerInterface');
        $internalNormalizer3 = $this->getMock('Yosmanyga\Resource\Normalizer\NormalizerInterface');
        $internalNormalizer2->expects($this->once())->method('supports')->will($this->returnValue(true));
        $normalizer = new DelegatorNormalizer(array($internalNormalizer1, $internalNormalizer2, $internalNormalizer3));
        $this->assertEquals($internalNormalizer2, $m->invoke($normalizer, '', new Resource()));
        $p = $r->getProperty('normalizers');
        $p->setAccessible(true);
        $this->assertEquals(array(0 => $internalNormalizer2, 1 => $internalNormalizer1, 2 => $internalNormalizer3), $p->getValue($normalizer));
    }

    /**
     * @covers Yosmanyga\Resource\Normalizer\DelegatorNormalizer::pickNormalizer
     * @expectedException \RuntimeException
     */
    public function testPickNormalizerThrowsExceptionWithNoValidNormalizer()
    {
        $r = new \ReflectionClass('Yosmanyga\Resource\Normalizer\DelegatorNormalizer');
        $m = $r->getMethod('pickNormalizer');
        $m->setAccessible(true);
        $internalNormalizer1 = $this->getMock('Yosmanyga\Resource\Normalizer\NormalizerInterface');
        $internalNormalizer1->expects($this->once())->method('supports')->will($this->returnValue(false));
        $delegatorNormalizer = new DelegatorNormalizer(array($internalNormalizer1));
        $m->invoke($delegatorNormalizer, null, new Resource());
    }
}
