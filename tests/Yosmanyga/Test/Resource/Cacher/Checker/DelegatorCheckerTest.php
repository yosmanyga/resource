<?php

namespace Yosmanyga\Test\Resource\Cacher\Checker;

use Yosmanyga\Resource\Cacher\Checker\DelegatorChecker;
use Yosmanyga\Resource\Cacher\Checker\DirectoryVersionChecker;
use Yosmanyga\Resource\Cacher\Checker\FileVersionChecker;
use Yosmanyga\Resource\Cacher\Checker\SerializedDataChecker;
use Yosmanyga\Resource\Resource;

class DelegatorCheckerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Yosmanyga\Resource\Cacher\Checker\DelegatorChecker::__construct
     */
    public function testConstructor()
    {
        $checker = new DelegatorChecker();
        $this->assertAttributeEquals(
            [
                new FileVersionChecker(),
                new DirectoryVersionChecker(),
                new SerializedDataChecker(),
            ],
            'checkers',
            $checker
        );

        $internalChecker1 = $this->getMock('Yosmanyga\Resource\Cacher\Checker\CheckerInterface');
        $checker = new DelegatorChecker([$internalChecker1]);
        $this->assertAttributeEquals([$internalChecker1], 'checkers', $checker);
    }

    /**
     * @covers Yosmanyga\Resource\Cacher\Checker\DelegatorChecker::supports
     */
    public function testSupports()
    {
        $resource = new Resource();

        $checker = $this->getMock('Yosmanyga\Resource\Cacher\Checker\DelegatorChecker', ['pickChecker']);
        $checker->expects($this->once())->method('pickChecker')->with($resource)->will($this->returnValue(true));
        $this->assertTrue($checker->supports($resource));

        $checker = $this->getMock('Yosmanyga\Resource\Cacher\Checker\DelegatorChecker', ['pickChecker']);
        $checker->expects($this->once())->method('pickChecker')->with($resource)->will($this->returnValue(false));
        $this->assertFalse($checker->supports($resource));
    }

    /**
     * @covers Yosmanyga\Resource\Cacher\Checker\DelegatorChecker::add
     */
    public function testAdd()
    {
        $resource = new Resource();
        $internalChecker = $this->getMock('Yosmanyga\Resource\Cacher\Checker\CheckerInterface');
        $internalChecker->expects($this->once())->method('supports')->will($this->returnValue(true));
        $internalChecker->expects($this->once())->method('add');
        $checker = new DelegatorChecker([$internalChecker]);
        $checker->add($resource);
    }

    /**
     * @covers Yosmanyga\Resource\Cacher\Checker\DelegatorChecker::check
     */
    public function testCheck()
    {
        $resource = new Resource();
        $internalChecker1 = $this->getMock('Yosmanyga\Resource\Cacher\Checker\CheckerInterface');
        $internalChecker2 = $this->getMock('Yosmanyga\Resource\Cacher\Checker\CheckerInterface');
        $internalChecker3 = $this->getMock('Yosmanyga\Resource\Cacher\Checker\CheckerInterface');
        $internalChecker2->expects($this->once())->method('supports')->will($this->returnValue(true));
        $internalChecker2->expects($this->once())->method('check');
        $checker = new DelegatorChecker([$internalChecker1, $internalChecker2, $internalChecker3]);
        $checker->check($resource);
    }

    /**
     * @covers Yosmanyga\Resource\Cacher\Checker\DelegatorChecker::pickChecker
     */
    public function testPickChecker()
    {
        $r = new \ReflectionClass('Yosmanyga\Resource\Cacher\Checker\DelegatorChecker');
        $m = $r->getMethod('pickChecker');
        $m->setAccessible(true);
        $internalChecker1 = $this->getMock('Yosmanyga\Resource\Cacher\Checker\CheckerInterface');
        $internalChecker2 = $this->getMock('Yosmanyga\Resource\Cacher\Checker\CheckerInterface');
        $internalChecker3 = $this->getMock('Yosmanyga\Resource\Cacher\Checker\CheckerInterface');
        $internalChecker2->expects($this->once())->method('supports')->will($this->returnValue(true));
        $checker = new DelegatorChecker([$internalChecker1, $internalChecker2, $internalChecker3]);
        $this->assertEquals($internalChecker2, $m->invoke($checker, new Resource()));
        $p = $r->getProperty('checkers');
        $p->setAccessible(true);
        $this->assertEquals([0 => $internalChecker2, 1 => $internalChecker1, 2 => $internalChecker3], $p->getValue($checker));
    }

    /**
     * @covers Yosmanyga\Resource\Cacher\Checker\DelegatorChecker::pickChecker
     * @expectedException \RuntimeException
     */
    public function testPickCheckerThrowsExceptionWithNoValidChecker()
    {
        $r = new \ReflectionClass('Yosmanyga\Resource\Cacher\Checker\DelegatorChecker');
        $m = $r->getMethod('pickChecker');
        $m->setAccessible(true);
        $internalChecker1 = $this->getMock('Yosmanyga\Resource\Cacher\Checker\CheckerInterface');
        $internalChecker1->expects($this->once())->method('supports')->will($this->returnValue(false));
        $checker = new DelegatorChecker([$internalChecker1]);
        $m->invoke($checker, new Resource());
    }
}
