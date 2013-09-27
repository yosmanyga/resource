<?php

namespace Yosmanyga\Test\Resource\Cacher\VersionChecker;

use Yosmanyga\Resource\Cacher\VersionChecker\DirectoryVersionChecker;
use Yosmanyga\Resource\Resource;
use Symfony\Component\Finder\Finder;

class DirectoryVersionCheckerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Yosmanyga\Resource\Cacher\VersionChecker\DirectoryVersionChecker::__construct
     */
    public function testConstructor()
    {
        $finder = $this->getMockBuilder('Symfony\Component\Finder\Finder')->disableOriginalConstructor()->getMock();
        /** @var \Symfony\Component\Finder\Finder $finder */
        $reader = new DirectoryVersionChecker($finder);
        $this->assertAttributeEquals($finder, 'finder', $reader);

        $reader = new DirectoryVersionChecker();
        $this->assertAttributeEquals(new Finder(), 'finder', $reader);
    }

    /**
     * @covers Yosmanyga\Resource\Cacher\VersionChecker\DirectoryVersionChecker::supports
     */
    public function testSupports()
    {
        $versionChecker = new DirectoryVersionChecker();

        // No dir metadata
        $this->assertFalse($versionChecker->supports(new Resource(array())));
        // Right metadata
        $this->assertTrue($versionChecker->supports(new Resource(array('dir' => 'foo'))));
    }

    /**
     * @covers Yosmanyga\Resource\Cacher\VersionChecker\DirectoryVersionChecker::get
     */
    public function testGet()
    {
        // With filter and depth metadata
        $resource = new Resource(array('dir' => sprintf("%s/Fixtures", dirname(__FILE__)), 'filter' => '*.yml', 'depth' => '== 0', 'type' => 'yaml'));
        $iterator = $this->getMock('\AppendIterator');
        $finder = $this->getMockBuilder('Symfony\Component\Finder\Finder')->disableOriginalConstructor()->getMock();
        /** @var \Symfony\Component\Finder\Finder $finder */
        $versionChecker = new DirectoryVersionChecker($finder);
        /** @var \PHPUnit_Framework_MockObject_MockObject $finder */
        $finder->expects($this->once())->method('files')->will($this->returnSelf());
        $finder->expects($this->once())->method('in')->with(sprintf("%s/Fixtures", dirname(__FILE__)));
        $finder->expects($this->once())->method('name')->with('*.yml');
        $finder->expects($this->once())->method('depth')->with('== 0');
        $finder->expects($this->once())->method('getIterator')->will($this->returnValue($iterator));
        $this->assertEquals(array(), $versionChecker->get($resource));

        // No filter metadata, no depth metadata
        $resource = new Resource(array('dir' => sprintf("%s/Fixtures", dirname(__FILE__)), 'type' => 'yaml'));
        $iterator = $this->getMock('\AppendIterator');
        $finder = $this->getMockBuilder('Symfony\Component\Finder\Finder')->disableOriginalConstructor()->getMock();
        /** @var \Symfony\Component\Finder\Finder $finder */
        $versionChecker = new DirectoryVersionChecker($finder);
        /** @var \PHPUnit_Framework_MockObject_MockObject $finder */
        $finder->expects($this->once())->method('files')->will($this->returnSelf());
        $finder->expects($this->once())->method('in')->with(sprintf("%s/Fixtures", dirname(__FILE__)));
        $finder->expects($this->once())->method('depth')->with('>= 0');
        $finder->expects($this->once())->method('getIterator')->will($this->returnValue($iterator));
        $this->assertEquals(array(), $versionChecker->get($resource));

        $versionChecker = new DirectoryVersionChecker();
        $versions = array();
        $versions[md5(sprintf("%s/Fixtures/foo.yml", dirname(__FILE__)))] = filemtime(sprintf("%s/Fixtures/foo.yml", dirname(__FILE__)));
        $this->assertEquals($versions, $versionChecker->get($resource));
    }

    /**
     * @covers Yosmanyga\Resource\Cacher\VersionChecker\DirectoryVersionChecker::get
     * @expectedException \InvalidArgumentException
     */
    public function testOpenThrowsExceptionWithInvalidDirMetadata()
    {
        $reader = new DirectoryVersionChecker();
        $reader->get(new Resource(array('dir' => '')));
    }
}
