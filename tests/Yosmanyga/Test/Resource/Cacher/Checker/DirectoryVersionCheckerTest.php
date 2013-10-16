<?php

namespace Yosmanyga\Test\Resource\Cacher\VersionChecker;

use Yosmanyga\Resource\Cacher\Checker\DirectoryVersionChecker;
use Yosmanyga\Resource\Resource;
use Symfony\Component\Finder\Finder;

class DirectoryVersionCheckerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Yosmanyga\Resource\Cacher\Checker\DirectoryVersionChecker::__construct
     */
    public function testConstructor()
    {
        $storer = $this->getMock('Yosmanyga\Resource\Cacher\Storer\StorerInterface');
        $finder = $this->getMockBuilder('Symfony\Component\Finder\Finder')->disableOriginalConstructor()->getMock();
        /** @var \Yosmanyga\Resource\Cacher\Storer\StorerInterface $storer */
        /** @var \Symfony\Component\Finder\Finder $finder */
        $checker = new DirectoryVersionChecker($storer, $finder);
        $this->assertAttributeEquals($storer, 'storer', $checker);
        $this->assertAttributeEquals($finder, 'finder', $checker);

        $checker = new DirectoryVersionChecker($storer);
        $this->assertAttributeEquals(new Finder(), 'finder', $checker);
    }

    /**
     * @covers Yosmanyga\Resource\Cacher\Checker\DirectoryVersionChecker::supports
     */
    public function testSupports()
    {
        $storer = $this->getMock('Yosmanyga\Resource\Cacher\Storer\StorerInterface');
        /** @var \Yosmanyga\Resource\Cacher\Storer\StorerInterface $storer */
        $checker = new DirectoryVersionChecker($storer);

        // No dir metadata
        $this->assertFalse($checker->supports(new Resource(array())));
        // Right metadata
        $this->assertTrue($checker->supports(new Resource(array('dir' => 'foo'))));
    }

    /**
     * @covers Yosmanyga\Resource\Cacher\Checker\DirectoryVersionChecker::add
     */
    public function testAdd()
    {
        $storer = $this->getMock('Yosmanyga\Resource\Cacher\Storer\StorerInterface');
        /** @var \Yosmanyga\Resource\Cacher\Storer\StorerInterface $storer */
        $checker = new DirectoryVersionChecker($storer);
        $m = new \ReflectionMethod($checker, 'calculateDirVersion');
        $m->setAccessible(true);
        $resource = new Resource(array('dir' => sprintf("%s/Fixtures", dirname(__FILE__)), 'filter' => '*.yml', 'depth' => '== 0', 'type' => 'yaml'));
        /** @var \PHPUnit_Framework_MockObject_MockObject $storer */
        $storer->expects($this->once())->method('add')->with($m->invoke($checker, $resource), $resource);
        $checker->add($resource);
    }

    /**
     * @covers Yosmanyga\Resource\Cacher\Checker\DirectoryVersionChecker::check
     */
    public function testCheck()
    {
        $resource = new Resource(array('dir' => sprintf("%s/Fixtures", dirname(__FILE__)), 'filter' => '*.yml', 'depth' => '== 0', 'type' => 'yaml'));

        $storer = $this->getMock('Yosmanyga\Resource\Cacher\Storer\StorerInterface');
        $storer->expects($this->once())->method('has')->with($resource)->will($this->returnValue(false));
        /** @var \Yosmanyga\Resource\Cacher\Storer\StorerInterface $storer */
        $checker = new DirectoryVersionChecker($storer);
        $this->assertFalse($checker->check($resource));

        $m = new \ReflectionMethod($checker, 'calculateDirVersion');
        $m->setAccessible(true);
        /** @var \PHPUnit_Framework_MockObject_MockObject $storer */
        $storer = $this->getMock('Yosmanyga\Resource\Cacher\Storer\StorerInterface');
        $storer->expects($this->once())->method('has')->with($resource)->will($this->returnValue(true));
        $storer->expects($this->once())->method('get')->with($resource)->will($this->returnValue($m->invoke($checker, $resource)));
        /** @var \Yosmanyga\Resource\Cacher\Storer\StorerInterface $storer */
        $checker = new DirectoryVersionChecker($storer);
        $this->assertTrue($checker->check($resource));

        /** @var \PHPUnit_Framework_MockObject_MockObject $storer */
        $storer = $this->getMock('Yosmanyga\Resource\Cacher\Storer\StorerInterface');
        $storer->expects($this->once())->method('has')->with($resource)->will($this->returnValue(true));
        $storer->expects($this->once())->method('get')->with($resource)->will($this->returnValue(array('123' => 123)));
        /** @var \Yosmanyga\Resource\Cacher\Storer\StorerInterface $storer */
        $checker = new DirectoryVersionChecker($storer);
        $this->assertFalse($checker->check($resource));
    }


    /**
     * @covers Yosmanyga\Resource\Cacher\Checker\DirectoryVersionChecker::calculateDirVersion
     */
    public function testCalculateDirVersion()
    {
        $r = new \ReflectionClass('Yosmanyga\Resource\Cacher\Checker\DirectoryVersionChecker');
        $m = $r->getMethod('calculateDirVersion');
        $m->setAccessible(true);
        
        // With filter and depth metadata
        $resource = new Resource(array('dir' => sprintf("%s/Fixtures", dirname(__FILE__)), 'filter' => '*.yml', 'depth' => '== 0', 'type' => 'yaml'));
        $iterator = $this->getMock('\AppendIterator');
        $storer = $this->getMock('Yosmanyga\Resource\Cacher\Storer\StorerInterface');
        /** @var \Yosmanyga\Resource\Cacher\Storer\StorerInterface $storer */
        $finder = $this->getMockBuilder('Symfony\Component\Finder\Finder')->disableOriginalConstructor()->getMock();
        /** @var \Symfony\Component\Finder\Finder $finder */
        $checker = new DirectoryVersionChecker($storer, $finder);
        /** @var \PHPUnit_Framework_MockObject_MockObject $finder */
        $finder->expects($this->once())->method('files')->will($this->returnSelf());
        $finder->expects($this->once())->method('in')->with(sprintf("%s/Fixtures", dirname(__FILE__)));
        $finder->expects($this->once())->method('name')->with('*.yml');
        $finder->expects($this->once())->method('depth')->with('== 0');
        $finder->expects($this->once())->method('getIterator')->will($this->returnValue($iterator));
        $this->assertEquals(array(), $m->invoke($checker, $resource));

        // No filter metadata, no depth metadata
        $resource = new Resource(array('dir' => sprintf("%s/Fixtures", dirname(__FILE__)), 'type' => 'yaml'));
        $iterator = $this->getMock('\AppendIterator');
        $storer = $this->getMock('Yosmanyga\Resource\Cacher\Storer\StorerInterface');
        /** @var \Yosmanyga\Resource\Cacher\Storer\StorerInterface $storer */
        $finder = $this->getMockBuilder('Symfony\Component\Finder\Finder')->disableOriginalConstructor()->getMock();
        /** @var \Symfony\Component\Finder\Finder $finder */
        $checker = new DirectoryVersionChecker($storer, $finder);
        /** @var \PHPUnit_Framework_MockObject_MockObject $finder */
        $finder->expects($this->once())->method('files')->will($this->returnSelf());
        $finder->expects($this->once())->method('in')->with(sprintf("%s/Fixtures", dirname(__FILE__)));
        $finder->expects($this->once())->method('depth')->with('>= 0');
        $finder->expects($this->once())->method('getIterator')->will($this->returnValue($iterator));
        $this->assertEquals(array(), $m->invoke($checker, $resource));

        $storer = $this->getMock('Yosmanyga\Resource\Cacher\Storer\StorerInterface');
        /** @var \Yosmanyga\Resource\Cacher\Storer\StorerInterface $storer */
        $checker = new DirectoryVersionChecker($storer);
        $versions = array();
        $versions[md5(sprintf("%s/Fixtures/foo.yml", dirname(__FILE__)))] = filemtime(sprintf("%s/Fixtures/foo.yml", dirname(__FILE__)));
        $this->assertEquals($versions, $m->invoke($checker, $resource));
    }

    /**
     * @covers Yosmanyga\Resource\Cacher\Checker\DirectoryVersionChecker::calculateDirVersion
     * @expectedException \InvalidArgumentException
     */
    public function testOpenThrowsExceptionWithInvalidDirMetadata()
    {
        $r = new \ReflectionClass('Yosmanyga\Resource\Cacher\Checker\DirectoryVersionChecker');
        $m = $r->getMethod('calculateDirVersion');
        $m->setAccessible(true);
        $storer = $this->getMock('Yosmanyga\Resource\Cacher\Storer\StorerInterface');
        /** @var \Yosmanyga\Resource\Cacher\Storer\StorerInterface $storer */
        $checker = new DirectoryVersionChecker($storer);
        $m->invoke($checker, new Resource(array('dir' => '')));
    }
}
