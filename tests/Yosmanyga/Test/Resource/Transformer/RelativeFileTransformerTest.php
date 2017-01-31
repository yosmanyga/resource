<?php

namespace Yosmanyga\Test\Resource\Transformer;

use Yosmanyga\Resource\Resource;
use Yosmanyga\Resource\Transformer\RelativeFileTransformer;

class RelativeFileTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Yosmanyga\Resource\Transformer\RelativeFileTransformer::__construct
     */
    public function testConstructor()
    {
        $transformer = new RelativeFileTransformer();
        $this->assertAttributeEquals(['@'], 'firstCharacters', $transformer);

        $transformer = new RelativeFileTransformer(['foo']);
        $this->assertAttributeEquals(['foo'], 'firstCharacters', $transformer);
    }

    /**
     * @covers Yosmanyga\Resource\Transformer\RelativeFileTransformer::supports
     */
    public function testSupports()
    {
        $transformer = new RelativeFileTransformer();

        $this->assertFalse($transformer->supports(
            new Resource(),
            new Resource()
        ));
        $this->assertFalse($transformer->supports(
            new Resource(['file' => '/bar/foo1.x']),
            new Resource()
        ));
        $this->assertFalse($transformer->supports(
            new Resource(['file' => 'c:/bar/foo1.x']),
            new Resource()
        ));
        $this->assertFalse($transformer->supports(
            new Resource(['file' => '@bar/foo1.x']),
            new Resource()
        ));
        $this->assertTrue($transformer->supports(
            new Resource(['file' => 'bar/foo1.x']),
            new Resource()
        ));
    }

    /**
     * @covers Yosmanyga\Resource\Transformer\RelativeFileTransformer::transform
     */
    public function testTransform()
    {
        $transformer = new RelativeFileTransformer();
        $this->assertEquals(
            new Resource(['file' => '/bar/foo1.x']),
            $transformer->transform(
                new Resource(['file' => 'foo1.x']),
                new Resource(['file' => '/bar/foo2.x'])
            )
        );
    }
}
