<?php

namespace Yosmanyga\Test\Resource\Cacher;

use Yosmanyga\Resource\Cacher\Cacher;
use Yosmanyga\Resource\Resource;

class CacherTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Yosmanyga\Resource\Cacher\Cacher::__construct
     */
    public function testConstructor()
    {
        /** @var \Yosmanyga\Resource\Cacher\Checker\CheckerInterface $checker */
        $checker = $this->getMock('Yosmanyga\Resource\Cacher\Checker\CheckerInterface');
        /** @var \Yosmanyga\Resource\Cacher\Storer\StorerInterface $storer */
        $storer = $this->getMock('Yosmanyga\Resource\Cacher\Storer\StorerInterface');
        $cacher = new Cacher($checker, $storer);
        $this->assertAttributeEquals($checker, 'checker', $cacher);
        $this->assertAttributeEquals($storer, 'storer', $cacher);
    }

    /**
     * @covers Yosmanyga\Resource\Cacher\Cacher::store
     */
    public function testStore()
    {
        $data = array();
        $resource = new Resource();

        $checker = $this->getMock('Yosmanyga\Resource\Cacher\Checker\CheckerInterface');
        $checker->expects($this->once())->method('add')->with($resource);
        $storer = $this->getMock('Yosmanyga\Resource\Cacher\Storer\StorerInterface');
        $storer->expects($this->once())->method('add')->with($data, $resource);
        /** @var \Yosmanyga\Resource\Cacher\Storer\StorerInterface $storer */
        /** @var \Yosmanyga\Resource\Cacher\Checker\CheckerInterface $checker */
        $cacher = new Cacher($checker, $storer);
        $cacher->store($data, $resource);
    }

    /**
     * @covers Yosmanyga\Resource\Cacher\Cacher::retrieve
     */
    public function testRetrieve()
    {
        $resource = new Resource();

        $storer = $this->getMock('Yosmanyga\Resource\Cacher\Storer\StorerInterface');
        $storer->expects($this->once())->method('get')->with($resource);
        $checker = $this->getMock('Yosmanyga\Resource\Cacher\Checker\CheckerInterface');
        /** @var \Yosmanyga\Resource\Cacher\Storer\StorerInterface $storer */
        /** @var \Yosmanyga\Resource\Cacher\Checker\CheckerInterface $checker */
        $cacher = new Cacher($checker, $storer);
        $cacher->retrieve($resource);
    }

    /**
     * @covers Yosmanyga\Resource\Cacher\Cacher::check
     */
    public function testCheck()
    {
        $resource = new Resource();

        $checker = $this->getMock('Yosmanyga\Resource\Cacher\Checker\CheckerInterface');
        $checker->expects($this->once())->method('check')->with($resource)->will($this->returnValue(true));
        $storer = $this->getMock('Yosmanyga\Resource\Cacher\Storer\StorerInterface');
        $storer->expects($this->once())->method('has')->with($resource)->will($this->returnValue(true));
        /** @var \Yosmanyga\Resource\Cacher\Storer\StorerInterface $storer */
        /** @var \Yosmanyga\Resource\Cacher\Checker\CheckerInterface $checker */
        $cacher = new Cacher($checker, $storer);
        $this->assertTrue($cacher->check($resource));

        $checker = $this->getMock('Yosmanyga\Resource\Cacher\Checker\CheckerInterface');
        /** @var \PHPUnit_Framework_MockObject_MockObject $checker */
        $checker->expects($this->once())->method('check')->with($resource)->will($this->returnValue(false));
        $storer = $this->getMock('Yosmanyga\Resource\Cacher\Storer\StorerInterface');
        /** @var \Yosmanyga\Resource\Cacher\Storer\StorerInterface $storer */
        /** @var \Yosmanyga\Resource\Cacher\Checker\CheckerInterface $checker */
        $cacher = new Cacher($checker, $storer);
        $this->assertFalse($cacher->check($resource));

        $checker = $this->getMock('Yosmanyga\Resource\Cacher\Checker\CheckerInterface');
        /** @var \PHPUnit_Framework_MockObject_MockObject $checker */
        $checker->expects($this->once())->method('check')->with($resource)->will($this->returnValue(true));
        $storer = $this->getMock('Yosmanyga\Resource\Cacher\Storer\StorerInterface');
        /** @var \PHPUnit_Framework_MockObject_MockObject $storer */
        $storer->expects($this->once())->method('has')->with($resource)->will($this->returnValue(false));
        /** @var \Yosmanyga\Resource\Cacher\Storer\StorerInterface $storer */
        /** @var \Yosmanyga\Resource\Cacher\Checker\CheckerInterface $checker */
        $cacher = new Cacher($checker, $storer);
        $this->assertFalse($cacher->check($resource));
    }


    /**
     * @covers Yosmanyga\Resource\Cacher\Cacher::__clone
     */
    public function testClone()
    {
        $checker = $this->getMock('Yosmanyga\Resource\Cacher\Checker\CheckerInterface');
        $storer = $this->getMock('Yosmanyga\Resource\Cacher\Storer\StorerInterface');
        /** @var \Yosmanyga\Resource\Cacher\Storer\StorerInterface $storer */
        /** @var \Yosmanyga\Resource\Cacher\Checker\CheckerInterface $checker */
        $cacher = new Cacher($checker, $storer);
        $r = new \ReflectionClass('Yosmanyga\Resource\Cacher\Cacher');
        $p1 = $r->getProperty('storer');
        $p1->setAccessible(true);
        $p2 = $r->getProperty('checker');
        $p2->setAccessible(true);
        $clone = clone $cacher;
        $this->assertNotSame($p1->getValue($cacher), $p1->getValue($clone));
        $this->assertNotSame($p2->getValue($cacher), $p2->getValue($clone));
    }
}
