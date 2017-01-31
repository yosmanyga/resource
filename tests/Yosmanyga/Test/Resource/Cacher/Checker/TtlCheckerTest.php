<?php

namespace Yosmanyga\Test\Resource\Cacher\Checker;

use Yosmanyga\Resource\Cacher\Checker\TtlChecker;
use Yosmanyga\Resource\Cacher\Storer\FileStorer;
use Yosmanyga\Resource\Resource;

class TtlCheckerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Yosmanyga\Resource\Cacher\Checker\TtlChecker::__construct
     */
    public function testConstructor()
    {
        $checker = new TtlChecker();
        $this->assertAttributeEquals(new FileStorer(), 'storer', $checker);

        /** @var \Yosmanyga\Resource\Cacher\Storer\StorerInterface $storer */
        $storer = $this->getMock('Yosmanyga\Resource\Cacher\Storer\StorerInterface');
        $ttl = 60;
        $checker = new TtlChecker($storer, $ttl);
        $this->assertAttributeEquals($storer, 'storer', $checker);
        $this->assertAttributeEquals($ttl, 'ttl', $checker);
    }

    /**
     * @covers Yosmanyga\Resource\Cacher\Checker\TtlChecker::supports
     */
    public function testSupports()
    {
        /** @var \Yosmanyga\Resource\Cacher\Storer\StorerInterface $storer */
        $storer = $this->getMock('Yosmanyga\Resource\Cacher\Storer\StorerInterface');
        $checker = new TtlChecker($storer);

        $this->assertTrue($checker->supports(new Resource()));
    }

    /**
     * @covers Yosmanyga\Resource\Cacher\Checker\TtlChecker::add
     */
    public function testAdd()
    {
        $resource = new Resource(['file' => __FILE__]);
        $storer = $this->getMock('Yosmanyga\Resource\Cacher\Storer\StorerInterface');
        $storer->expects($this->once())->method('add')->with();
        /** @var \Yosmanyga\Resource\Cacher\Storer\StorerInterface $storer */
        $checker = new TtlChecker($storer);
        $checker->add($resource);
    }

    /**
     * @covers Yosmanyga\Resource\Cacher\Checker\TtlChecker::check
     */
    public function testCheck()
    {
        $resource = new Resource(['file' => __FILE__]);

        $storer = $this->getMock('Yosmanyga\Resource\Cacher\Storer\StorerInterface');
        $storer->expects($this->once())->method('has')->with($resource)->will($this->returnValue(false));
        /** @var \Yosmanyga\Resource\Cacher\Storer\StorerInterface $storer */
        $checker = new TtlChecker($storer);
        $this->assertFalse($checker->check($resource));

        /** @var \PHPUnit_Framework_MockObject_MockObject $storer */
        $storer = $this->getMock('Yosmanyga\Resource\Cacher\Storer\StorerInterface');
        $storer->expects($this->once())->method('has')->with($resource)->will($this->returnValue(true));
        $storer->expects($this->once())->method('get')->with($resource)->will($this->returnValue(time() + 1));
        /** @var \Yosmanyga\Resource\Cacher\Storer\StorerInterface $storer */
        $checker = new TtlChecker($storer);
        $this->assertTrue($checker->check($resource));

        /** @var \PHPUnit_Framework_MockObject_MockObject $storer */
        $storer = $this->getMock('Yosmanyga\Resource\Cacher\Storer\StorerInterface');
        $storer->expects($this->once())->method('has')->with($resource)->will($this->returnValue(true));
        $storer->expects($this->once())->method('get')->with($resource)->will($this->returnValue(time() - 1));
        /** @var \Yosmanyga\Resource\Cacher\Storer\StorerInterface $storer */
        $checker = new TtlChecker($storer);
        $this->assertFalse($checker->check($resource));
    }
}
