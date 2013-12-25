<?php

namespace Yosmanyga\Test\Resource\Transformer;

use Yosmanyga\Resource\Transformer\DelegatorTransformer;
use Yosmanyga\Resource\Transformer\RelativeFileTransformer;
use Yosmanyga\Resource\Transformer\RelativeDirectoryTransformer;
use Yosmanyga\Resource\Transformer\AbsoluteFileTransformer;
use Yosmanyga\Resource\Transformer\AbsoluteDirectoryTransformer;
use Yosmanyga\Resource\Resource;

class DelegatorTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Yosmanyga\Resource\Transformer\DelegatorTransformer::__construct
     */
    public function testConstructor()
    {
        $delegatorTransformer = new DelegatorTransformer();
        $this->assertAttributeEquals(
            array(
                new RelativeFileTransformer(),
                new RelativeDirectoryTransformer(),
                new AbsoluteFileTransformer(),
                new AbsoluteDirectoryTransformer()
            ),
            'transformers',
            $delegatorTransformer
        );

        $internalTransformer1 = $this->getMock('Yosmanyga\Resource\Transformer\TransformerInterface');
        $delegatorTransformer = new DelegatorTransformer(array($internalTransformer1));
        $this->assertAttributeEquals(array($internalTransformer1), 'transformers', $delegatorTransformer);
    }

    /**
     * @covers Yosmanyga\Resource\Transformer\DelegatorTransformer::supports
     */
    public function testSupports()
    {
        $internalTransformer1 = $this->getMock('Yosmanyga\Resource\Transformer\TransformerInterface');
        $internalTransformer2 = $this->getMock('Yosmanyga\Resource\Transformer\TransformerInterface');
        $internalTransformer3 = $this->getMock('Yosmanyga\Resource\Transformer\TransformerInterface');
        $internalTransformer2->expects($this->once())->method('supports')->will($this->returnValue(true));
        $delegatorTransformer = new DelegatorTransformer(array($internalTransformer1,$internalTransformer2, $internalTransformer3));
        $this->assertTrue($delegatorTransformer->supports(new Resource(), new Resource()));
        $this->assertAttributeEquals(array(0 => $internalTransformer2, 1 => $internalTransformer1, 3 => $internalTransformer3), 'transformers', $delegatorTransformer);

        $internalTransformer1 = $this->getMock('Yosmanyga\Resource\Transformer\TransformerInterface');
        $internalTransformer1->expects($this->once())->method('supports')->will($this->returnValue(false));
        $delegatorTransformer = new DelegatorTransformer(array($internalTransformer1));
        $this->assertFalse($delegatorTransformer->supports(new Resource(), new Resource()));
    }

    /**
     * @covers Yosmanyga\Resource\Transformer\DelegatorTransformer::transform
     */
    public function testTransform()
    {
        $internalTransformer1 = $this->getMock('Yosmanyga\Resource\Transformer\TransformerInterface');
        $internalTransformer1->expects($this->once())->method('supports')->will($this->returnValue(true));
        $internalTransformer1->expects($this->once())->method('transform');
        $delegatorTransformer = new DelegatorTransformer(array($internalTransformer1));
        $delegatorTransformer->transform(new Resource(), new Resource());
    }

    /**
     * @covers Yosmanyga\Resource\Transformer\DelegatorTransformer::pickTransformer
     */
    public function testPickTransformer()
    {
        $r = new \ReflectionClass('Yosmanyga\Resource\Transformer\DelegatorTransformer');
        $m = $r->getMethod('pickTransformer');
        $m->setAccessible(true);
        $internalTransformer1 = $this->getMock('Yosmanyga\Resource\Transformer\TransformerInterface');
        $internalTransformer2 = $this->getMock('Yosmanyga\Resource\Transformer\TransformerInterface');
        $internalTransformer3 = $this->getMock('Yosmanyga\Resource\Transformer\TransformerInterface');
        $internalTransformer2->expects($this->once())->method('supports')->will($this->returnValue(true));
        $delegatorTransformer = new DelegatorTransformer(array($internalTransformer1, $internalTransformer2, $internalTransformer3));
        $this->assertEquals($internalTransformer2, $m->invoke($delegatorTransformer, new Resource(), new Resource()));
        $this->assertAttributeEquals(array(0 => $internalTransformer2, 1 => $internalTransformer1, 3 => $internalTransformer3), 'transformers', $delegatorTransformer);
    }

    /**
     * @covers Yosmanyga\Resource\Transformer\DelegatorTransformer::pickTransformer
     * @expectedException \RuntimeException
     */
    public function testPickTransformerThrowsExceptionWithNoValidTransformer()
    {
        $r = new \ReflectionClass('Yosmanyga\Resource\Transformer\DelegatorTransformer');
        $m = $r->getMethod('pickTransformer');
        $m->setAccessible(true);
        $internalTransformer1 = $this->getMock('Yosmanyga\Resource\Transformer\TransformerInterface');
        $internalTransformer1->expects($this->once())->method('supports')->will($this->returnValue(false));
        $delegatorTransformer = new DelegatorTransformer(array($internalTransformer1));
        $m->invoke($delegatorTransformer, new Resource(), new Resource());
    }
}
