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
        $dataStorer = $this->getMock('Yosmanyga\Resource\Cacher\Storer\StorerInterface');
        $versionStorer = $this->getMock('Yosmanyga\Resource\Cacher\Storer\StorerInterface');
        $versionChecker = $this->getMock('Yosmanyga\Resource\Cacher\VersionChecker\VersionCheckerInterface');
        /** @var \Yosmanyga\Resource\Cacher\Storer\StorerInterface $dataStorer */
        /** @var \Yosmanyga\Resource\Cacher\Storer\StorerInterface $versionStorer */
        /** @var \Yosmanyga\Resource\Cacher\VersionChecker\VersionCheckerInterface $versionChecker */
        $cacher = new Cacher($dataStorer, $versionStorer, $versionChecker);
        $this->assertAttributeEquals($dataStorer, 'dataStorer', $cacher);
        $this->assertAttributeEquals($versionStorer, 'versionStorer', $cacher);
        $this->assertAttributeEquals($versionChecker, 'versionChecker', $cacher);
    }

    /**
     * @covers Yosmanyga\Resource\Cacher\Cacher::store
     */
    public function testStore()
    {
        $data = array();
        $resource = new Resource();

        $dataStorer = $this->getMock('Yosmanyga\Resource\Cacher\Storer\StorerInterface');
        $dataStorer->expects($this->once())->method('add')->with($data, $resource);
        $versionChecker = $this->getMock('Yosmanyga\Resource\Cacher\VersionChecker\VersionCheckerInterface');
        $versionChecker->expects($this->once())->method('get')->with($resource)->will($this->returnValue('111'));
        $versionStorer = $this->getMock('Yosmanyga\Resource\Cacher\Storer\StorerInterface');
        $versionStorer->expects($this->once())->method('add')->with(111, $resource);
        /** @var \Yosmanyga\Resource\Cacher\Storer\StorerInterface $dataStorer */
        /** @var \Yosmanyga\Resource\Cacher\Storer\StorerInterface $versionStorer */
        /** @var \Yosmanyga\Resource\Cacher\VersionChecker\VersionCheckerInterface $versionChecker */
        $cacher = new Cacher($dataStorer, $versionStorer, $versionChecker);
        $cacher->store($data, $resource);
    }

    /**
     * @covers Yosmanyga\Resource\Cacher\Cacher::retrieve
     */
    public function testRetrieve()
    {
        $resource = new Resource();

        $dataStorer = $this->getMock('Yosmanyga\Resource\Cacher\Storer\StorerInterface');
        $dataStorer->expects($this->once())->method('get')->with($resource);
        $versionChecker = $this->getMock('Yosmanyga\Resource\Cacher\VersionChecker\VersionCheckerInterface');
        $versionStorer = $this->getMock('Yosmanyga\Resource\Cacher\Storer\StorerInterface');
        /** @var \Yosmanyga\Resource\Cacher\Storer\StorerInterface $dataStorer */
        /** @var \Yosmanyga\Resource\Cacher\Storer\StorerInterface $versionStorer */
        /** @var \Yosmanyga\Resource\Cacher\VersionChecker\VersionCheckerInterface $versionChecker */
        $cacher = new Cacher($dataStorer, $versionStorer, $versionChecker);
        $cacher->retrieve($resource);
    }

    /**
     * @covers Yosmanyga\Resource\Cacher\Cacher::check
     */
    public function testCheckWithNoVersionCache()
    {
        $resource = new Resource();

        $dataStorer = $this->getMock('Yosmanyga\Resource\Cacher\Storer\StorerInterface');
        $versionChecker = $this->getMock('Yosmanyga\Resource\Cacher\VersionChecker\VersionCheckerInterface');
        $versionStorer = $this->getMock('Yosmanyga\Resource\Cacher\Storer\StorerInterface');
        /** @var \Yosmanyga\Resource\Cacher\Storer\StorerInterface $dataStorer */
        /** @var \Yosmanyga\Resource\Cacher\Storer\StorerInterface $versionStorer */
        /** @var \Yosmanyga\Resource\Cacher\VersionChecker\VersionCheckerInterface $versionChecker */
        $cacher = new Cacher($dataStorer, $versionStorer, $versionChecker);
        /** @var \PHPUnit_Framework_MockObject_MockObject $versionChecker */
        /** @var \PHPUnit_Framework_MockObject_MockObject $versionStorer */
        $versionStorer->expects($this->once())->method('has')->with($resource)->will($this->returnValue(false));
        $this->assertFalse($cacher->check($resource));
    }

    /**
     * @covers Yosmanyga\Resource\Cacher\Cacher::check
     */
    public function testCheckWithDifferentVersions()
    {
        $resource = new Resource();

        $dataStorer = $this->getMock('Yosmanyga\Resource\Cacher\Storer\StorerInterface');
        $versionChecker = $this->getMock('Yosmanyga\Resource\Cacher\VersionChecker\VersionCheckerInterface');
        $versionStorer = $this->getMock('Yosmanyga\Resource\Cacher\Storer\StorerInterface');
        /** @var \Yosmanyga\Resource\Cacher\Storer\StorerInterface $dataStorer */
        /** @var \Yosmanyga\Resource\Cacher\Storer\StorerInterface $versionStorer */
        /** @var \Yosmanyga\Resource\Cacher\VersionChecker\VersionCheckerInterface $versionChecker */
        $cacher = new Cacher($dataStorer, $versionStorer, $versionChecker);
        /** @var \PHPUnit_Framework_MockObject_MockObject $versionChecker */
        /** @var \PHPUnit_Framework_MockObject_MockObject $versionStorer */
        $versionStorer->expects($this->once())->method('has')->with($resource)->will($this->returnValue(true));
        $versionChecker->expects($this->once())->method('get')->with($resource)->will($this->returnValue(111));
        $versionStorer->expects($this->once())->method('get')->with($resource)->will($this->returnValue(112));
        $this->assertFalse($cacher->check($resource));
    }

    /**
     * @covers Yosmanyga\Resource\Cacher\Cacher::check
     */
    public function testCheckWithSameVersions()
    {
        $resource = new Resource();

        $dataStorer = $this->getMock('Yosmanyga\Resource\Cacher\Storer\StorerInterface');
        $versionChecker = $this->getMock('Yosmanyga\Resource\Cacher\VersionChecker\VersionCheckerInterface');
        $versionStorer = $this->getMock('Yosmanyga\Resource\Cacher\Storer\StorerInterface');
        /** @var \Yosmanyga\Resource\Cacher\Storer\StorerInterface $dataStorer */
        /** @var \Yosmanyga\Resource\Cacher\Storer\StorerInterface $versionStorer */
        /** @var \Yosmanyga\Resource\Cacher\VersionChecker\VersionCheckerInterface $versionChecker */
        $cacher = new Cacher($dataStorer, $versionStorer, $versionChecker);
        /** @var \PHPUnit_Framework_MockObject_MockObject $versionChecker */
        /** @var \PHPUnit_Framework_MockObject_MockObject $versionStorer */
        $versionStorer->expects($this->once())->method('has')->with($resource)->will($this->returnValue(true));
        $versionChecker->expects($this->once())->method('get')->with($resource)->will($this->returnValue(111));
        $versionStorer->expects($this->once())->method('get')->with($resource)->will($this->returnValue(111));
        $this->assertTrue($cacher->check($resource));
    }

    /**
     * @covers Yosmanyga\Resource\Cacher\Cacher::__clone
     */
    public function testClone()
    {
        $dataStorer = $this->getMock('Yosmanyga\Resource\Cacher\Storer\StorerInterface');
        $versionChecker = $this->getMock('Yosmanyga\Resource\Cacher\VersionChecker\VersionCheckerInterface');
        $versionStorer = $this->getMock('Yosmanyga\Resource\Cacher\Storer\StorerInterface');
        /** @var \Yosmanyga\Resource\Cacher\Storer\StorerInterface $dataStorer */
        /** @var \Yosmanyga\Resource\Cacher\Storer\StorerInterface $versionStorer */
        /** @var \Yosmanyga\Resource\Cacher\VersionChecker\VersionCheckerInterface $versionChecker */
        $cacher = new Cacher($dataStorer, $versionStorer, $versionChecker);
        $r = new \ReflectionClass('Yosmanyga\Resource\Cacher\Cacher');
        $p1 = $r->getProperty('dataStorer');
        $p1->setAccessible(true);
        $p2 = $r->getProperty('versionStorer');
        $p2->setAccessible(true);
        $p3 = $r->getProperty('versionChecker');
        $p3->setAccessible(true);
        $clone = clone $cacher;
        $this->assertNotSame($p1->getValue($cacher), $p1->getValue($clone));
        $this->assertNotSame($p2->getValue($cacher), $p2->getValue($clone));
        $this->assertNotSame($p3->getValue($cacher), $p3->getValue($clone));
    }
}
