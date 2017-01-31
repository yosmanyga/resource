<?php

namespace Yosmanyga\Test\Resource\Transformer;

use Yosmanyga\Resource\Resource;
use Yosmanyga\Resource\Transformer\AbsoluteDirectoryTransformer;

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
            new Resource(['dir' => 'bar/foo1']),
            new Resource()
        ));
        $this->assertTrue($transformer->supports(
            new Resource(['dir' => '/bar/foo1']),
            new Resource()
        ));
        $this->assertTrue($transformer->supports(
            new Resource(['dir' => 'c:/bar/foo1']),
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
            new Resource(['file' => '/bar/foo1.x']),
            $transformer->transform(
                new Resource(['file' => '/bar/foo1.x']),
                new Resource()
            )
        );
    }
}
