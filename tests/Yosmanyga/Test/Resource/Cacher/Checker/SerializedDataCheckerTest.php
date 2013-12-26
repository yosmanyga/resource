<?php

namespace Yosmanyga\Test\Resource\Cacher\Checker;

use Yosmanyga\Resource\Cacher\Checker\SerializedDataChecker;
use Yosmanyga\Resource\Cacher\Storer\CheckFileStorer;
use Yosmanyga\Resource\Resource;

class SerializedDataCheckerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Yosmanyga\Resource\Cacher\Checker\SerializedDataChecker::__construct
     */
    public function testConstructor()
    {
        $checker = new SerializedDataChecker();
        $this->assertAttributeEquals(new CheckFileStorer(), 'storer', $checker);

        /** @var \Yosmanyga\Resource\Cacher\Storer\StorerInterface $storer */
        $storer = $this->getMock('Yosmanyga\Resource\Cacher\Storer\StorerInterface');
        $checker = new SerializedDataChecker($storer);
        $this->assertAttributeEquals($storer, 'storer', $checker);
    }

    /**
     * @covers Yosmanyga\Resource\Cacher\Checker\SerializedDataChecker::supports
     */
    public function testSupports()
    {
        /** @var \Yosmanyga\Resource\Cacher\Storer\StorerInterface $storer */
        $storer = $this->getMock('Yosmanyga\Resource\Cacher\Storer\StorerInterface');
        $checker = new SerializedDataChecker($storer);

        // No data metadata
        $this->assertFalse($checker->supports(new Resource(array())));
        // Right metadata
        $this->assertTrue($checker->supports(new Resource(array('data' => 'foo'))));
    }

    /**
     * @covers Yosmanyga\Resource\Cacher\Checker\SerializedDataChecker::add
     */
    public function testAdd()
    {
        $resource = new Resource(array('data' => 'foo'));
        $storer = $this->getMock('Yosmanyga\Resource\Cacher\Storer\StorerInterface');
        $storer->expects($this->once())->method('add')->with(serialize($resource->getMetadata('data')), $resource);
        /** @var \Yosmanyga\Resource\Cacher\Storer\StorerInterface $storer */
        $checker = new SerializedDataChecker($storer);
        $checker->add($resource);
    }

    /**
     * @covers Yosmanyga\Resource\Cacher\Checker\SerializedDataChecker::check
     */
    public function testCheck()
    {
        $resource = new Resource(array('data' => 'foo'));

        $storer = $this->getMock('Yosmanyga\Resource\Cacher\Storer\StorerInterface');
        $storer->expects($this->once())->method('has')->with($resource)->will($this->returnValue(false));
        /** @var \Yosmanyga\Resource\Cacher\Storer\StorerInterface $storer */
        $checker = new SerializedDataChecker($storer);
        $this->assertFalse($checker->check($resource));

        /** @var \PHPUnit_Framework_MockObject_MockObject $storer */
        $storer = $this->getMock('Yosmanyga\Resource\Cacher\Storer\StorerInterface');
        $storer->expects($this->once())->method('has')->with($resource)->will($this->returnValue(true));
        $storer->expects($this->once())->method('get')->with($resource)->will($this->returnValue(serialize($resource->getMetadata('data'))));
        /** @var \Yosmanyga\Resource\Cacher\Storer\StorerInterface $storer */
        $checker = new SerializedDataChecker($storer);
        $this->assertTrue($checker->check($resource));

        /** @var \PHPUnit_Framework_MockObject_MockObject $storer */
        $storer = $this->getMock('Yosmanyga\Resource\Cacher\Storer\StorerInterface');
        $storer->expects($this->once())->method('has')->with($resource)->will($this->returnValue(true));
        $storer->expects($this->once())->method('get')->with($resource)->will($this->returnValue(serialize('bar')));
        /** @var \Yosmanyga\Resource\Cacher\Storer\StorerInterface $storer */
        $checker = new SerializedDataChecker($storer);
        $this->assertFalse($checker->check($resource));
    }
}
