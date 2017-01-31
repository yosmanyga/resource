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
        $this->assertAttributeEquals([], 'normalizers', $delegatorNormalizer);

        $internalNormalizer1 = $this->getMock('Yosmanyga\Resource\Normalizer\NormalizerInterface');
        $delegatorNormalizer = new DelegatorNormalizer([$internalNormalizer1]);
        $this->assertAttributeEquals([$internalNormalizer1], 'normalizers', $delegatorNormalizer);
    }

    /**
     * @covers Yosmanyga\Resource\Normalizer\DelegatorNormalizer::supports
     */
    public function testSupports()
    {
        $data = [];
        $resource = new Resource();

        $normalizer = $this->getMock('Yosmanyga\Resource\Normalizer\DelegatorNormalizer', ['pickNormalizer']);
        $normalizer->expects($this->once())->method('pickNormalizer')->with($data, $resource)->will($this->returnValue(true));
        $this->assertTrue($normalizer->supports($data, $resource));

        $normalizer = $this->getMock('Yosmanyga\Resource\Normalizer\DelegatorNormalizer', ['pickNormalizer']);
        $normalizer->expects($this->once())->method('pickNormalizer')->with($data, $resource)->will($this->returnValue(false));
        $this->assertFalse($normalizer->supports($data, $resource));
    }

    /**
     * @covers Yosmanyga\Resource\Normalizer\DelegatorNormalizer::normalize
     */
    public function testNormalize()
    {
        $data = [];
        $resource = new Resource();
        $internalNormalizer1 = $this->getMock('Yosmanyga\Resource\Normalizer\NormalizerInterface');
        $internalNormalizer1->expects($this->once())->method('supports')->will($this->returnValue(true));
        $internalNormalizer1->expects($this->once())->method('normalize')->with($data, $resource);
        $delegatorNormalizer = new DelegatorNormalizer([$internalNormalizer1]);
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
        $normalizer = new DelegatorNormalizer([$internalNormalizer1, $internalNormalizer2, $internalNormalizer3]);
        $this->assertEquals($internalNormalizer2, $m->invoke($normalizer, '', new Resource()));
        $p = $r->getProperty('normalizers');
        $p->setAccessible(true);
        $this->assertEquals([0 => $internalNormalizer2, 1 => $internalNormalizer1, 2 => $internalNormalizer3], $p->getValue($normalizer));
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
        $delegatorNormalizer = new DelegatorNormalizer([$internalNormalizer1]);
        $m->invoke($delegatorNormalizer, null, new Resource());
    }
}
