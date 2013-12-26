<?php

namespace Yosmanyga\Test\Resource\Cacher\Checker;

use Yosmanyga\Resource\Cacher\Checker\FileVersionChecker;
use Yosmanyga\Resource\Cacher\Storer\CheckFileStorer;
use Yosmanyga\Resource\Resource;

class FileVersionCheckerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Yosmanyga\Resource\Cacher\Checker\FileVersionChecker::__construct
     */
    public function testConstructor()
    {
        $checker = new FileVersionChecker();
        $this->assertAttributeEquals(new CheckFileStorer(), 'storer', $checker);

        $storer = $this->getMock('Yosmanyga\Resource\Cacher\Storer\StorerInterface');
        /** @var \Yosmanyga\Resource\Cacher\Storer\StorerInterface $storer */
        $checker = new FileVersionChecker($storer);
        $this->assertAttributeEquals($storer, 'storer', $checker);
    }

    /**
     * @covers Yosmanyga\Resource\Cacher\Checker\FileVersionChecker::supports
     */
    public function testSupports()
    {
        /** @var \Yosmanyga\Resource\Cacher\Storer\StorerInterface $storer */
        $storer = $this->getMock('Yosmanyga\Resource\Cacher\Storer\StorerInterface');
        $checker = new FileVersionChecker($storer);

        // No file metadata
        $this->assertFalse($checker->supports(new Resource(array())));
        // Right metadata
        $this->assertTrue($checker->supports(new Resource(array('file' => 'foo'))));
    }

    /**
     * @covers Yosmanyga\Resource\Cacher\Checker\FileVersionChecker::add
     */
    public function testAdd()
    {
        $resource = new Resource(array('file' => __FILE__));
        $storer = $this->getMock('Yosmanyga\Resource\Cacher\Storer\StorerInterface');
        $storer->expects($this->once())->method('add')->with(filemtime($resource->getMetadata('file')), $resource);
        /** @var \Yosmanyga\Resource\Cacher\Storer\StorerInterface $storer */
        $checker = new FileVersionChecker($storer);
        $checker->add($resource);
    }

    /**
     * @covers Yosmanyga\Resource\Cacher\Checker\FileVersionChecker::check
     */
    public function testCheck()
    {
        $resource = new Resource(array('file' => sprintf("%s/Fixtures/foo.yml", dirname(__FILE__))));

        $storer = $this->getMock('Yosmanyga\Resource\Cacher\Storer\StorerInterface');
        $storer->expects($this->once())->method('has')->with($resource)->will($this->returnValue(false));
        /** @var \Yosmanyga\Resource\Cacher\Storer\StorerInterface $storer */
        $checker = new FileVersionChecker($storer);
        $checker->add($resource);
        $this->assertFalse($checker->check($resource));

        /** @var \PHPUnit_Framework_MockObject_MockObject $storer */
        $storer = $this->getMock('Yosmanyga\Resource\Cacher\Storer\StorerInterface');
        $storer->expects($this->once())->method('has')->with($resource)->will($this->returnValue(true));
        $storer->expects($this->once())->method('get')->with($resource)->will($this->returnValue(filemtime($resource->getMetadata('file'))));
        /** @var \Yosmanyga\Resource\Cacher\Storer\StorerInterface $storer */
        $checker = new FileVersionChecker($storer);
        $checker->add($resource);
        $this->assertTrue($checker->check($resource));

        /** @var \PHPUnit_Framework_MockObject_MockObject $storer */
        $storer = $this->getMock('Yosmanyga\Resource\Cacher\Storer\StorerInterface');
        $storer->expects($this->once())->method('has')->with($resource)->will($this->returnValue(true));
        $storer->expects($this->once())->method('get')->with($resource)->will($this->returnValue(123));
        /** @var \Yosmanyga\Resource\Cacher\Storer\StorerInterface $storer */
        $checker = new FileVersionChecker($storer);
        $checker->add($resource);
        $this->assertFalse($checker->check($resource));
    }
}
