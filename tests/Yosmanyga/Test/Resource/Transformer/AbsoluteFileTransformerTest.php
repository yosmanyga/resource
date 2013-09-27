<?php

namespace Yosmanyga\Test\Resource\Transformer;

use Yosmanyga\Resource\Transformer\AbsoluteFileTransformer;
use Yosmanyga\Resource\Resource;

class AbsoluteFileTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Yosmanyga\Resource\Transformer\AbsoluteFileTransformer::supports
     */
    public function testSupports()
    {
        $transformer = new AbsoluteFileTransformer();

        $this->assertFalse($transformer->supports(
            new Resource(),
            new Resource()
        ));
        $this->assertFalse($transformer->supports(
            new Resource(array('file' => 'bar/foo1.x')),
            new Resource()
        ));
        $this->assertTrue($transformer->supports(
            new Resource(array('file' => '/bar/foo1.x')),
            new Resource()
        ));
        $this->assertTrue($transformer->supports(
            new Resource(array('file' => 'c:/bar/foo1.x')),
            new Resource()
        ));
    }

    /**
     * @covers Yosmanyga\Resource\Transformer\AbsoluteFileTransformer::transform
     */
    public function testTransform()
    {
        $transformer = new AbsoluteFileTransformer();
        $this->assertEquals(
            new Resource(array('file' => '/bar/foo1.x')),
            $transformer->transform(
                new Resource(array('file' => '/bar/foo1.x')),
                new Resource()
            )
        );
    }
}
