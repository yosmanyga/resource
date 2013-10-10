<?php

namespace Yosmanyga\Test\Resource\Transformer;

use Yosmanyga\Resource\Transformer\AbsoluteDirectoryTransformer;
use Yosmanyga\Resource\Resource;

class AbsoluteDirectoryTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Yosmanyga\Resource\Transformer\AbsoluteDirectoryTransformer::supports
     */
    public function testSupports()
    {
        $transformer = new AbsoluteDirectoryTransformer();

        $this->assertFalse($transformer->supports(
            new Resource(),
            new Resource()
        ));
        $this->assertFalse($transformer->supports(
            new Resource(array('dir' => 'bar/foo1')),
            new Resource()
        ));
        $this->assertTrue($transformer->supports(
            new Resource(array('dir' => '/bar/foo1')),
            new Resource()
        ));
        $this->assertTrue($transformer->supports(
            new Resource(array('dir' => 'c:/bar/foo1')),
            new Resource()
        ));
    }

    /**
     * @covers Yosmanyga\Resource\Transformer\AbsoluteDirectoryTransformer::transform
     */
    public function testTransform()
    {
        $transformer = new AbsoluteDirectoryTransformer();
        $this->assertEquals(
            new Resource(array('file' => '/bar/foo1.x')),
            $transformer->transform(
                new Resource(array('file' => '/bar/foo1.x')),
                new Resource()
            )
        );
    }
}
