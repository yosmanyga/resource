<?php

namespace Yosmanyga\Test\Resource\Transformer;

use Yosmanyga\Resource\Resource;
use Yosmanyga\Resource\Transformer\RelativeDirectoryTransformer;

class RelativeDirectoryTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Yosmanyga\Resource\Transformer\RelativeDirectoryTransformer::__construct
     */
    public function testConstructor()
    {
        $transformer = new RelativeDirectoryTransformer();
        $this->assertAttributeEquals(['@'], 'firstCharacters', $transformer);

        $transformer = new RelativeDirectoryTransformer(['foo']);
        $this->assertAttributeEquals(['foo'], 'firstCharacters', $transformer);
    }

    /**
     * @covers Yosmanyga\Resource\Transformer\RelativeDirectoryTransformer::supports
     */
    public function testSupports()
    {
        $transformer = new RelativeDirectoryTransformer();

        $this->assertFalse($transformer->supports(
            new Resource(),
            new Resource()
        ));
        $this->assertFalse($transformer->supports(
            new Resource(['dir' => '/bar/foo1']),
            new Resource()
        ));
        $this->assertFalse($transformer->supports(
            new Resource(['dir' => 'c:/bar/foo1']),
            new Resource()
        ));
        $this->assertFalse($transformer->supports(
            new Resource(['dir' => '@bar/foo1']),
            new Resource()
        ));
        $this->assertTrue($transformer->supports(
            new Resource(['dir' => 'bar/foo1']),
            new Resource()
        ));
    }

    /**
     * @covers Yosmanyga\Resource\Transformer\RelativeDirectoryTransformer::transform
     */
    public function testTransform()
    {
        $transformer = new RelativeDirectoryTransformer();
        $this->assertEquals(
            new Resource(['file' => '/bar/foo1']),
            $transformer->transform(
                new Resource(['dir' => 'foo1']),
                new Resource(['file' => '/bar/foo2.x'])
            )
        );
    }
}
