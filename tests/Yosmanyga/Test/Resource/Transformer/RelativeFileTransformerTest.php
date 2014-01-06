<?php

namespace Yosmanyga\Test\Resource\Transformer;

use Yosmanyga\Resource\Transformer\RelativeFileTransformer;
use Yosmanyga\Resource\Resource;

class RelativeFileTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Yosmanyga\Resource\Transformer\RelativeFileTransformer::construct
     */
    public function testConstructor()
    {
        $transformer = new RelativeFileTransformer();
        $this->assertAttributeEquals(array('@'), 'firstCharacters', $transformer);

        $transformer = new RelativeFileTransformer(array('foo'));
        $this->assertAttributeEquals(array('foo'), 'firstCharacters', $transformer);
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
            new Resource(array('file' => '/bar/foo1.x')),
            new Resource()
        ));
        $this->assertFalse($transformer->supports(
            new Resource(array('file' => 'c:/bar/foo1.x')),
            new Resource()
        ));
        $this->assertFalse($transformer->supports(
            new Resource(array('file' => '@bar/foo1.x')),
            new Resource()
        ));
        $this->assertTrue($transformer->supports(
            new Resource(array('file' => 'bar/foo1.x')),
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
            new Resource(array('file' => '/bar/foo1.x')),
            $transformer->transform(
                new Resource(array('file' => 'foo1.x')),
                new Resource(array('file' => '/bar/foo2.x'))
            )
        );
    }
}
