<?php

namespace Yosmanyga\Test\Resource\Reader\Iterator;

use Symfony\Component\Finder\Finder;
use Yosmanyga\Resource\Reader\Iterator\DirectoryReader;
use Yosmanyga\Resource\Reader\Iterator\IniFileReader;
use Yosmanyga\Resource\Reader\Iterator\SuddenAnnotationFileReader;
use Yosmanyga\Resource\Reader\Iterator\XmlFileReader;
use Yosmanyga\Resource\Reader\Iterator\YamlFileReader;
use Yosmanyga\Resource\Resource;

class DirectoryReaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Yosmanyga\Resource\Reader\Iterator\DirectoryReader::__construct
     */
    public function testConstructor()
    {
        $reader = new DirectoryReader();
        $p = new \ReflectionProperty($reader, 'delegatorReader');
        $p->setAccessible(true);
        $delegatorReader = $p->getValue($reader);
        $this->assertAttributeEquals(
            [
                new IniFileReader(),
                new YamlFileReader(),
                new XmlFileReader(),
                new SuddenAnnotationFileReader(),
            ],
            'readers',
            $delegatorReader
        );
        $this->assertAttributeEquals(new Finder(), 'finder', $reader);

        $finder = $this->getMockBuilder('Symfony\Component\Finder\Finder')->disableOriginalConstructor()->getMock();
        /** @var \Symfony\Component\Finder\Finder $finder */
        $readers = [new IniFileReader()];
        $reader = new DirectoryReader($readers, $finder);
        $p = new \ReflectionProperty($reader, 'delegatorReader');
        $p->setAccessible(true);
        $delegatorReader = $p->getValue($reader);
        $this->assertAttributeEquals($readers, 'readers', $delegatorReader);
        $this->assertAttributeEquals($finder, 'finder', $reader);
    }

    /**
     * @covers Yosmanyga\Resource\Reader\Iterator\DirectoryReader::supports
     */
    public function testSupports()
    {
        $reader = new DirectoryReader();

        // Right type
        $this->assertTrue($reader->supports(new Resource([], 'directory')));
        // Wrong type
        $this->assertFalse($reader->supports(new Resource([], 'foo')));
        // No type, no dir metadata
        $this->assertFalse($reader->supports(new Resource(['type' => 'annotation'])));
        // No type, no type metadata
        $this->assertFalse($reader->supports(new Resource(['dir' => '/foo'])));
        // No type, dir and type metadata
        $this->assertTrue($reader->supports(new Resource(['dir' => '/foo', 'type' => 'annotation'])));
    }

    /**
     * @covers Yosmanyga\Resource\Reader\Iterator\DirectoryReader::open
     */
    public function testOpen()
    {
        $resource = new Resource(['dir' => sprintf('%s/Fixtures/directory/', dirname(__FILE__)), 'filter' => '*.yml', 'type' => 'yaml']);
        $delegatorReader = $this->getMock('Yosmanyga\Resource\Reader\Iterator\DelegatorReader');
        $reader = new DirectoryReader();
        $p = new \ReflectionProperty($reader, 'delegatorReader');
        $p->setAccessible(true);
        $p->setValue($reader, $delegatorReader);
        $delegatorReader->expects($this->once())->method('open');
        $reader->open($resource);
        $this->assertAttributeEquals($resource, 'resource', $reader);
    }

    /**
     * @covers Yosmanyga\Resource\Reader\Iterator\DirectoryReader::current
     */
    public function testCurrent()
    {
        $reader = new DirectoryReader();
        $delegatorReader = $this->getMock('Yosmanyga\Resource\Reader\Iterator\DelegatorReader');
        $p = new \ReflectionProperty($reader, 'delegatorReader');
        $p->setAccessible(true);
        $p->setValue($reader, $delegatorReader);
        $iterator = $this->getMock('\AppendIterator');
        $p = new \ReflectionProperty($reader, 'iterator');
        $p->setAccessible(true);
        $p->setValue($reader, $iterator);
        $delegatorReader->expects($this->once())->method('current')->will($this->returnValue(['foo']));
        $iterator->expects($this->never())->method('next');
        $reader->current();

        $reader = new DirectoryReader();
        $delegatorReader = $this->getMock('Yosmanyga\Resource\Reader\Iterator\DelegatorReader');
        $p = new \ReflectionProperty($reader, 'delegatorReader');
        $p->setAccessible(true);
        $p->setValue($reader, $delegatorReader);
        $iterator = $this->getMock('\AppendIterator');
        $p = new \ReflectionProperty($reader, 'iterator');
        $p->setAccessible(true);
        $p->setValue($reader, $iterator);
        $delegatorReader->expects($this->once())->method('current')->will($this->returnValue(false));
        $iterator->expects($this->once())->method('next');
        $iterator->expects($this->once())->method('valid')->will($this->returnValue(false));
        $this->assertFalse($reader->current());

        $reader = new DirectoryReader();
        $delegatorReader = $this->getMock('Yosmanyga\Resource\Reader\Iterator\DelegatorReader');
        $p = new \ReflectionProperty($reader, 'delegatorReader');
        $p->setAccessible(true);
        $p->setValue($reader, $delegatorReader);
        $iterator = $this->getMock('\AppendIterator');
        $p = new \ReflectionProperty($reader, 'iterator');
        $p->setAccessible(true);
        $p->setValue($reader, $iterator);
        $p = new \ReflectionProperty($reader, 'resource');
        $p->setAccessible(true);
        $p->setValue($reader, new Resource(['dir' => sprintf('%s/Fixtures/directory/', dirname(__FILE__)), 'filter' => '*.yml', 'depth' => '1', 'type' => 'yaml']));
        $delegatorReader->expects($this->at(1))->method('current')->will($this->returnValue(false));
        $iterator->expects($this->once())->method('next');
        $iterator->expects($this->once())->method('valid')->will($this->returnValue(true));
        $file = $this->getMockBuilder('\SplFileInfo')->disableOriginalConstructor()->getMock();
        $iterator->expects($this->once())->method('current')->will($this->returnValue($file));
        $delegatorReader->expects($this->once())->method('open');
        $delegatorReader->expects($this->at(2))->method('current')->will($this->returnValue(true));
        $reader->current();
    }

    /**
     * @covers Yosmanyga\Resource\Reader\Iterator\DirectoryReader::next
     */
    public function testNext()
    {
        $delegatorReader = $this->getMock('Yosmanyga\Resource\Reader\Iterator\DelegatorReader');
        $reader = new DirectoryReader();
        $p = new \ReflectionProperty($reader, 'delegatorReader');
        $p->setAccessible(true);
        $p->setValue($reader, $delegatorReader);
        $delegatorReader->expects($this->once())->method('next');
        $reader->next();
    }

    /**
     * @covers Yosmanyga\Resource\Reader\Iterator\DirectoryReader::close
     */
    public function testClose()
    {
        $delegatorReader = $this->getMock('Yosmanyga\Resource\Reader\Iterator\DelegatorReader');
        $reader = new DirectoryReader();
        $p = new \ReflectionProperty($reader, 'delegatorReader');
        $p->setAccessible(true);
        $p->setValue($reader, $delegatorReader);
        $delegatorReader->expects($this->once())->method('close');
        $reader->close();
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testPrepareFinderThrowsExceptionWhenResourceNotSet()
    {
        $reader = new DirectoryReader();
        $m = new \ReflectionMethod('Yosmanyga\Resource\Reader\Iterator\DirectoryReader', 'prepareFinder');
        $m->setAccessible(true);
        $m->invoke($reader);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testPrepareFinderThrowsExceptionWhenInvalidDir()
    {
        $reader = new DirectoryReader();
        $m = new \ReflectionMethod('Yosmanyga\Resource\Reader\Iterator\DirectoryReader', 'prepareFinder');
        $m->setAccessible(true);
        $p = new \ReflectionProperty('Yosmanyga\Resource\Reader\Iterator\DirectoryReader', 'resource');
        $p->setAccessible(true);
        $p->setValue($reader, new Resource(['dir' => '']));
        $m->invoke($reader);
    }

    public function testPrepareFinder()
    {
        $finder = $this->getMockBuilder('Symfony\Component\Finder\Finder')->disableOriginalConstructor()->getMock();
        $iterator = $this->getMock('\AppendIterator');
        /** @var \Symfony\Component\Finder\Finder $finder */
        $reader = new DirectoryReader([], $finder);
        $p = new \ReflectionProperty('Yosmanyga\Resource\Reader\Iterator\DirectoryReader', 'resource');
        $p->setAccessible(true);
        $p->setValue($reader, new Resource(['dir' => sprintf('%s/Fixtures/directory/', dirname(__FILE__)), 'filter' => '*.yml', 'depth' => '1']));
        $m = new \ReflectionMethod('Yosmanyga\Resource\Reader\Iterator\DirectoryReader', 'prepareFinder');
        $m->setAccessible(true);
        /* @var \PHPUnit_Framework_MockObject_MockObject $finder */
        $finder->expects($this->once())->method('files')->will($this->returnSelf());
        $finder->expects($this->once())->method('in')->with(sprintf('%s/Fixtures/directory/', dirname(__FILE__)));
        $finder->expects($this->once())->method('name')->with('*.yml');
        $finder->expects($this->once())->method('depth')->with('1');
        $finder->expects($this->once())->method('getIterator')->will($this->returnValue($iterator));
        $iterator->expects($this->once())->method('rewind');
        $m->invoke($reader);

        // No depth metadata
        $finder = $this->getMockBuilder('Symfony\Component\Finder\Finder')->disableOriginalConstructor()->getMock();
        $iterator = $this->getMock('\AppendIterator');
        /** @var \Symfony\Component\Finder\Finder $finder */
        $reader = new DirectoryReader([], $finder);
        $p = new \ReflectionProperty('Yosmanyga\Resource\Reader\Iterator\DirectoryReader', 'resource');
        $p->setAccessible(true);
        $p->setValue($reader, new Resource(['dir' => sprintf('%s/Fixtures/directory/', dirname(__FILE__)), 'filter' => '*.yml']));
        $m = new \ReflectionMethod('Yosmanyga\Resource\Reader\Iterator\DirectoryReader', 'prepareFinder');
        $m->setAccessible(true);
        /* @var \PHPUnit_Framework_MockObject_MockObject $finder */
        $finder->expects($this->once())->method('files')->will($this->returnSelf());
        $finder->expects($this->once())->method('in')->with(sprintf('%s/Fixtures/directory/', dirname(__FILE__)));
        $finder->expects($this->once())->method('depth')->with('>= 0');
        $finder->expects($this->once())->method('getIterator')->will($this->returnValue($iterator));
        $iterator->expects($this->once())->method('rewind');
        $m->invoke($reader);
    }

    public function testConvertResource()
    {
        $resource = new Resource(['type' => 'yaml']);
        $filename = sprintf('%s/Fixtures/foo.yml', dirname(__FILE__));
        $file = new \SplFileInfo($filename);
        $reader = new DirectoryReader();
        $m = new \ReflectionMethod($reader, 'convertResource');
        $m->setAccessible(true);
        $this->assertEquals(new Resource(['file' => $filename, 'type' => 'yaml'], 'yaml'), $m->invoke($reader, $resource, $file));
    }
}
