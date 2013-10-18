<?php

namespace Yosmanyga\Test\Resource\Reader\Iterator;

use Symfony\Component\Finder\Finder;
use Yosmanyga\Resource\Reader\Iterator\DelegatorReader;
use Yosmanyga\Resource\Reader\Iterator\DirectoryReader;
use Yosmanyga\Resource\Resource;

class DirectoryReaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Yosmanyga\Resource\Reader\Iterator\DirectoryReader::__construct
     */
    public function testConstructor()
    {
        $delegatorReader = $this->getMock('Yosmanyga\Resource\Reader\Iterator\DelegatorReader');
        $finder = $this->getMockBuilder('Symfony\Component\Finder\Finder')->disableOriginalConstructor()->getMock();
        /** @var \Yosmanyga\Resource\Reader\Iterator\DelegatorReader $delegatorReader */
        /** @var \Symfony\Component\Finder\Finder $finder */
        $reader = new DirectoryReader($delegatorReader, $finder);
        $this->assertAttributeEquals($delegatorReader, 'delegatorReader', $reader);
        $this->assertAttributeEquals($finder, 'finder', $reader);

        /** @var \Yosmanyga\Resource\Reader\Iterator\DelegatorReader $delegatorReader */
        $delegatorReader = $this->getMock('Yosmanyga\Resource\Reader\Iterator\DelegatorReader');
        $reader = new DirectoryReader($delegatorReader);
        $this->assertAttributeEquals(new Finder(), 'finder', $reader);
    }

    /**
     * @covers Yosmanyga\Resource\Reader\Iterator\DirectoryReader::supports
     */
    public function testSupports()
    {
        $reader = new DirectoryReader(new DelegatorReader());

        // Right type
        $this->assertTrue($reader->supports(new Resource(array(), 'directory')));
        // Wrong type
        $this->assertFalse($reader->supports(new Resource(array(), 'foo')));
        // No type, no dir metadata
        $this->assertFalse($reader->supports(new Resource(array('type' => 'annotation'))));
        // No type, no type metadata
        $this->assertFalse($reader->supports(new Resource(array('dir' => '/foo'))));
        // No type, dir and type metadata
        $this->assertTrue($reader->supports(new Resource(array('dir' => '/foo', 'type' => 'annotation'))));
    }

    /**
     * @covers Yosmanyga\Resource\Reader\Iterator\DirectoryReader::open
     */
    public function testOpen()
    {
        $resource = new Resource(array('dir' => sprintf("%s/Fixtures/directory/", dirname(__FILE__)), 'filter' => '*.yml', 'type' => 'yaml'));
        $delegatorReader = $this->getMock('Yosmanyga\Resource\Reader\Iterator\DelegatorReader');
        /** @var \Yosmanyga\Resource\Reader\Iterator\DelegatorReader $delegatorReader */
        $reader = new DirectoryReader($delegatorReader);
        /** @var \PHPUnit_Framework_MockObject_MockObject $delegatorReader */
        $delegatorReader->expects($this->once())->method('open');
        $reader->open($resource);
        $this->assertAttributeEquals($resource, 'resource', $reader);
    }

    /**
     * @covers Yosmanyga\Resource\Reader\Iterator\DirectoryReader::current
     */
    public function testCurrent()
    {
        $delegatorReader = $this->getMock('Yosmanyga\Resource\Reader\Iterator\DelegatorReader');
        $delegatorReader->expects($this->once())->method('current');
        /** @var \Yosmanyga\Resource\Reader\Iterator\DelegatorReader $delegatorReader */
        $reader = new DirectoryReader($delegatorReader);
        $reader->current();
    }

    /**
     * @covers Yosmanyga\Resource\Reader\Iterator\DirectoryReader::next
     */
    public function testNext()
    {
        $delegatorReader = $this->getMock('Yosmanyga\Resource\Reader\Iterator\DelegatorReader');
        /** @var \Yosmanyga\Resource\Reader\Iterator\DelegatorReader $delegatorReader */
        $reader = new DirectoryReader($delegatorReader);
        /** @var \PHPUnit_Framework_MockObject_MockObject $delegatorReader */
        $delegatorReader->expects($this->once())->method('next');
        $delegatorReader->expects($this->once())->method('current')->will($this->returnValue(true));
        $reader->next();

        $delegatorReader = $this->getMock('Yosmanyga\Resource\Reader\Iterator\DelegatorReader');
        $iterator = $this->getMock('\AppendIterator');
        $file = $this->getMockBuilder('\SplFileInfo')->disableOriginalConstructor()->getMock();
        /** @var \Yosmanyga\Resource\Reader\Iterator\DelegatorReader $delegatorReader */
        $reader = new DirectoryReader($delegatorReader);
        $p = new \ReflectionProperty('Yosmanyga\Resource\Reader\Iterator\DirectoryReader', 'resource');
        $p->setAccessible(true);
        $p->setValue($reader, new Resource(array('dir' => sprintf("%s/Fixtures/directory/", dirname(__FILE__)), 'filter' => '*.yml', 'depth' => '1', 'type' => 'yaml')));
        $p = new \ReflectionProperty('Yosmanyga\Resource\Reader\Iterator\DirectoryReader', 'iterator');
        $p->setAccessible(true);
        $p->setValue($reader, $iterator);
        /** @var \PHPUnit_Framework_MockObject_MockObject $delegatorReader */
        $delegatorReader->expects($this->once())->method('next')->will($this->returnValue(false));
        $delegatorReader->expects($this->once())->method('current')->will($this->returnValue(false));
        $iterator->expects($this->once())->method('valid')->will($this->returnValue(true));
        $iterator->expects($this->once())->method('current')->will($this->returnValue($file));
        $file->expects($this->once())->method('getRealpath')->will($this->returnValue('foo2.bar'));
        $reader->next();
    }

    /**
     * @covers Yosmanyga\Resource\Reader\Iterator\DirectoryReader::close
     */
    public function testClose()
    {
        $delegatorReader = $this->getMock('Yosmanyga\Resource\Reader\Iterator\DelegatorReader');
        $delegatorReader->expects($this->once())->method('close');
        /** @var \Yosmanyga\Resource\Reader\Iterator\DelegatorReader $delegatorReader */
        $reader = new DirectoryReader($delegatorReader);
        $reader->close();
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testPrepareFinderThrowsExceptionWhenResourceNotSet()
    {
        $reader = new DirectoryReader(new DelegatorReader());
        $m = new \ReflectionMethod('Yosmanyga\Resource\Reader\Iterator\DirectoryReader', 'prepareFinder');
        $m->setAccessible(true);
        $m->invoke($reader);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testPrepareFinderThrowsExceptionWhenInvalidDir()
    {
        $reader = new DirectoryReader(new DelegatorReader());
        $m = new \ReflectionMethod('Yosmanyga\Resource\Reader\Iterator\DirectoryReader', 'prepareFinder');
        $m->setAccessible(true);
        $p = new \ReflectionProperty('Yosmanyga\Resource\Reader\Iterator\DirectoryReader', 'resource');
        $p->setAccessible(true);
        $p->setValue($reader, new Resource(array('dir' => '')));
        $m->invoke($reader);
    }

    public function testPrepareFinder()
    {
        $finder = $this->getMockBuilder('Symfony\Component\Finder\Finder')->disableOriginalConstructor()->getMock();
        $iterator = $this->getMock('\AppendIterator');
        /** @var \Symfony\Component\Finder\Finder $finder */
        $reader = new DirectoryReader(new DelegatorReader(), $finder);
        $p = new \ReflectionProperty('Yosmanyga\Resource\Reader\Iterator\DirectoryReader', 'resource');
        $p->setAccessible(true);
        $p->setValue($reader, new Resource(array('dir' => sprintf("%s/Fixtures/directory/", dirname(__FILE__)), 'filter' => '*.yml', 'depth' => '1')));
        $m = new \ReflectionMethod('Yosmanyga\Resource\Reader\Iterator\DirectoryReader', 'prepareFinder');
        $m->setAccessible(true);
        /** @var \PHPUnit_Framework_MockObject_MockObject $finder */
        $finder->expects($this->once())->method('files')->will($this->returnSelf());
        $finder->expects($this->once())->method('in')->with(sprintf("%s/Fixtures/directory/", dirname(__FILE__)));
        $finder->expects($this->once())->method('name')->with('*.yml');
        $finder->expects($this->once())->method('depth')->with('1');
        $finder->expects($this->once())->method('getIterator')->will($this->returnValue($iterator));
        $iterator->expects($this->once())->method('rewind');
        $m->invoke($reader);

        // No depth metadata
        $finder = $this->getMockBuilder('Symfony\Component\Finder\Finder')->disableOriginalConstructor()->getMock();
        $iterator = $this->getMock('\AppendIterator');
        /** @var \Symfony\Component\Finder\Finder $finder */
        $reader = new DirectoryReader(new DelegatorReader(), $finder);
        $p = new \ReflectionProperty('Yosmanyga\Resource\Reader\Iterator\DirectoryReader', 'resource');
        $p->setAccessible(true);
        $p->setValue($reader, new Resource(array('dir' => sprintf("%s/Fixtures/directory/", dirname(__FILE__)), 'filter' => '*.yml')));
        $m = new \ReflectionMethod('Yosmanyga\Resource\Reader\Iterator\DirectoryReader', 'prepareFinder');
        $m->setAccessible(true);
        /** @var \PHPUnit_Framework_MockObject_MockObject $finder */
        $finder->expects($this->once())->method('files')->will($this->returnSelf());
        $finder->expects($this->once())->method('in')->with(sprintf("%s/Fixtures/directory/", dirname(__FILE__)));
        $finder->expects($this->once())->method('depth')->with('>= 0');
        $finder->expects($this->once())->method('getIterator')->will($this->returnValue($iterator));
        $iterator->expects($this->once())->method('rewind');
        $m->invoke($reader);
    }

    public function testConvertResource()
    {
        $resource = new Resource(array('type' => 'yaml'));
        $filename = sprintf("%s/Fixtures/foo.yml", dirname(__FILE__));
        $file = new \SplFileInfo($filename);
        $reader = new DirectoryReader(new DelegatorReader());
        $m = new \ReflectionMethod($reader, 'convertResource');
        $m->setAccessible(true);
        $this->assertEquals(new Resource(array('file' => $filename, 'type' => 'yaml'), 'yaml'), $m->invoke($reader, $resource, $file));
    }
}
