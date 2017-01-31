<?php

namespace Yosmanyga\Test\Resource;

use Yosmanyga\Resource\Resource;

class ResourceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Yosmanyga\Resource\Resource::__construct
     */
    public function testSupports()
    {
        $resource = new Resource([
            'foo1' => 'bar1',
            'foo2' => 'bar2',
        ], 'foo');

        $this->assertEquals(['foo1' => 'bar1', 'foo2' => 'bar2'], $resource->getMetadata());
        $this->assertEquals('foo', $resource->getType());
    }

    /**
     * @covers Yosmanyga\Resource\Resource::setMetadata
     * @covers Yosmanyga\Resource\Resource::getMetadata
     * @covers Yosmanyga\Resource\Resource::hasMetadata
     * @covers Yosmanyga\Resource\Resource::setType
     * @covers Yosmanyga\Resource\Resource::getType
     * @covers Yosmanyga\Resource\Resource::hasType
     */
    public function testAccessors()
    {
        $resource = new Resource();
        $resource->setMetadata('foo1', 'bar1');
        $resource->setMetadata('foo2', 'bar2');
        $resource->setType('foo');

        $this->assertTrue($resource->hasMetadata('foo1'));
        $this->assertEquals('bar1', $resource->getMetadata('foo1'));
        $this->assertEquals(['foo1' => 'bar1', 'foo2' => 'bar2'], $resource->getMetadata());
        $this->assertFalse($resource->hasMetadata('foo3'));
        $this->assertEquals('foo', $resource->getType());
        $this->assertTrue($resource->hasType());

        $resource = new Resource();

        $this->assertFalse($resource->hasType());
    }
}
