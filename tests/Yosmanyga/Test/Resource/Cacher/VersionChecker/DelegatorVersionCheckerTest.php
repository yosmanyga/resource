<?php

namespace Yosmanyga\Test\Resource\Cacher\VersionChecker;

use Yosmanyga\Resource\Cacher\VersionChecker\DelegatorVersionChecker;
use Yosmanyga\Resource\Resource;

class DelegatorVersionCheckerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Yosmanyga\Resource\Cacher\VersionChecker\DelegatorVersionChecker::__construct
     */
    public function testConstructor()
    {
        $versionChecker = new DelegatorVersionChecker();
        $this->assertAttributeEquals(array(), 'versionCheckers', $versionChecker);

        $internalVersionChecker1 = $this->getMock('Yosmanyga\Resource\Cacher\VersionChecker\VersionCheckerInterface');
        $versionChecker = new DelegatorVersionChecker(array($internalVersionChecker1));
        $this->assertAttributeEquals(array($internalVersionChecker1), 'versionCheckers', $versionChecker);
    }

    /**
     * @covers Yosmanyga\Resource\Cacher\VersionChecker\DelegatorVersionChecker::supports
     */
    public function testSupports()
    {
        $internalVersionChecker1 = $this->getMock('Yosmanyga\Resource\Cacher\VersionChecker\VersionCheckerInterface');
        $internalVersionChecker2 = $this->getMock('Yosmanyga\Resource\Cacher\VersionChecker\VersionCheckerInterface');
        $internalVersionChecker3 = $this->getMock('Yosmanyga\Resource\Cacher\VersionChecker\VersionCheckerInterface');
        $internalVersionChecker2->expects($this->once())->method('supports')->will($this->returnValue(true));
        $versionChecker = new DelegatorVersionChecker(array($internalVersionChecker1, $internalVersionChecker2, $internalVersionChecker3));
        $this->assertTrue($versionChecker->supports(new Resource()));
        $this->assertAttributeEquals(array(0 => $internalVersionChecker2, 1 => $internalVersionChecker1, 3 => $internalVersionChecker3), 'versionCheckers', $versionChecker);

        $internalVersionChecker1 = $this->getMock('Yosmanyga\Resource\Cacher\VersionChecker\VersionCheckerInterface');
        $internalVersionChecker1->expects($this->once())->method('supports')->will($this->returnValue(false));
        $versionChecker = new DelegatorVersionChecker(array($internalVersionChecker1));
        $this->assertFalse($versionChecker->supports(new Resource()));
    }

    /**
     * @covers Yosmanyga\Resource\Cacher\VersionChecker\DelegatorVersionChecker::get
     */
    public function testGet()
    {
        $resource = new Resource();
        $internalVersionChecker1 = $this->getMock('Yosmanyga\Resource\Cacher\VersionChecker\VersionCheckerInterface');
        $internalVersionChecker2 = $this->getMock('Yosmanyga\Resource\Cacher\VersionChecker\VersionCheckerInterface');
        $internalVersionChecker3 = $this->getMock('Yosmanyga\Resource\Cacher\VersionChecker\VersionCheckerInterface');
        $internalVersionChecker2->expects($this->once())->method('supports')->will($this->returnValue(true));
        $internalVersionChecker2->expects($this->once())->method('get');
        $versionChecker = new DelegatorVersionChecker(array($internalVersionChecker1, $internalVersionChecker2, $internalVersionChecker3));
        $versionChecker->get($resource);
    }

    /**
     * @covers Yosmanyga\Resource\Cacher\VersionChecker\DelegatorVersionChecker::pickVersionChecker
     */
    public function testPickVersionChecker()
    {
        $r = new \ReflectionClass('Yosmanyga\Resource\Cacher\VersionChecker\DelegatorVersionChecker');
        $m = $r->getMethod('pickVersionChecker');
        $m->setAccessible(true);
        $internalVersionChecker1 = $this->getMock('Yosmanyga\Resource\Cacher\VersionChecker\VersionCheckerInterface');
        $internalVersionChecker2 = $this->getMock('Yosmanyga\Resource\Cacher\VersionChecker\VersionCheckerInterface');
        $internalVersionChecker3 = $this->getMock('Yosmanyga\Resource\Cacher\VersionChecker\VersionCheckerInterface');
        $internalVersionChecker2->expects($this->once())->method('supports')->will($this->returnValue(true));
        $versionChecker = new DelegatorVersionChecker(array($internalVersionChecker1, $internalVersionChecker2, $internalVersionChecker3));
        $this->assertEquals($internalVersionChecker2, $m->invoke($versionChecker, new Resource()));
        $p = $r->getProperty('versionCheckers');
        $p->setAccessible(true);
        $this->assertEquals(array(0 => $internalVersionChecker2, 1 => $internalVersionChecker1, 3 => $internalVersionChecker3), $p->getValue($versionChecker));
    }

    /**
     * @covers Yosmanyga\Resource\Cacher\VersionChecker\DelegatorVersionChecker::pickVersionChecker
     * @expectedException \RuntimeException
     */
    public function testPickVersionCheckerThrowsExceptionWithNoValidVersionChecker()
    {
        $r = new \ReflectionClass('Yosmanyga\Resource\Cacher\VersionChecker\DelegatorVersionChecker');
        $m = $r->getMethod('pickVersionChecker');
        $m->setAccessible(true);
        $internalVersionChecker1 = $this->getMock('Yosmanyga\Resource\Cacher\VersionChecker\VersionCheckerInterface');
        $internalVersionChecker1->expects($this->once())->method('supports')->will($this->returnValue(false));
        $versionChecker = new DelegatorVersionChecker(array($internalVersionChecker1));
        $m->invoke($versionChecker, new Resource());
    }
}
