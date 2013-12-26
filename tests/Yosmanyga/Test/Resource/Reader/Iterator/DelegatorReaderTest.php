<?php

namespace Yosmanyga\Test\Resource\Reader\Iterator;

use Yosmanyga\Resource\Reader\Iterator\DelegatorReader;
use Yosmanyga\Resource\Reader\Iterator\IniFileReader;
use Yosmanyga\Resource\Reader\Iterator\YamlFileReader;
use Yosmanyga\Resource\Reader\Iterator\XmlFileReader;
use Yosmanyga\Resource\Reader\Iterator\SuddenAnnotationFileReader;
use Yosmanyga\Resource\Reader\Iterator\DirectoryReader;
use Yosmanyga\Resource\Resource;

class DelegatorReaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Yosmanyga\Resource\Reader\Iterator\DelegatorReader::__construct
     */
    public function testConstructor()
    {
        $reader = new DelegatorReader();
        $this->assertAttributeEquals(
            array(
                new IniFileReader(),
                new YamlFileReader(),
                new XmlFileReader(),
                new SuddenAnnotationFileReader(),
                new DirectoryReader()
            ),
            'readers',
            $reader
        );

        $internalReader1 = $this->getMock('Yosmanyga\Resource\Reader\Iterator\ReaderInterface');
        $reader = new DelegatorReader(array($internalReader1));
        $this->assertAttributeEquals(array($internalReader1), 'readers', $reader);
    }

    /**
     * @covers Yosmanyga\Resource\Reader\Iterator\DelegatorReader::supports
     */
    public function testSupports()
    {
        $internalReader1 = $this->getMock('Yosmanyga\Resource\Reader\Iterator\ReaderInterface');
        $internalReader2 = $this->getMock('Yosmanyga\Resource\Reader\Iterator\ReaderInterface');
        $internalReader3 = $this->getMock('Yosmanyga\Resource\Reader\Iterator\ReaderInterface');
        $internalReader2->expects($this->once())->method('supports')->will($this->returnValue(true));
        $reader = new DelegatorReader(array($internalReader1, $internalReader2, $internalReader3));
        $this->assertTrue($reader->supports(new Resource()));
        $this->assertAttributeEquals(array(0 => $internalReader2, 1 => $internalReader1, 2 => $internalReader3), 'readers', $reader);

        $internalReader1 = $this->getMock('Yosmanyga\Resource\Reader\Iterator\ReaderInterface');
        $internalReader1->expects($this->once())->method('supports')->will($this->returnValue(false));
        $reader = new DelegatorReader(array($internalReader1));
        $this->assertFalse($reader->supports(new Resource()));
    }

    /**
     * @covers Yosmanyga\Resource\Reader\Iterator\DelegatorReader::open
     */
    public function testOpen()
    {
        $resource = new Resource();
        $internalReader1 = $this->getMock('Yosmanyga\Resource\Reader\Iterator\ReaderInterface');
        $internalReader2 = $this->getMock('Yosmanyga\Resource\Reader\Iterator\ReaderInterface');
        $internalReader3 = $this->getMock('Yosmanyga\Resource\Reader\Iterator\ReaderInterface');
        $internalReader2->expects($this->once())->method('supports')->will($this->returnValue(true));
        $internalReader2->expects($this->once())->method('open');
        $reader = new DelegatorReader(array($internalReader1, $internalReader2, $internalReader3));
        $reader->open($resource);
    }

    /**
     * @covers Yosmanyga\Resource\Reader\Iterator\DelegatorReader::current
     */
    public function testCurrent()
    {
        $internalReader1 = $this->getMock('Yosmanyga\Resource\Reader\Iterator\ReaderInterface');
        $internalReader1->expects($this->once())->method('supports')->will($this->returnValue(true));
        $internalReader1->expects($this->once())->method('current');
        $reader = new DelegatorReader(array($internalReader1));
        $reader->open(new Resource());
        $reader->current();
    }

    /**
     * @covers Yosmanyga\Resource\Reader\Iterator\DelegatorReader::next
     */
    public function testNext()
    {
        $internalReader1 = $this->getMock('Yosmanyga\Resource\Reader\Iterator\ReaderInterface');
        $internalReader1->expects($this->once())->method('supports')->will($this->returnValue(true));
        $internalReader1->expects($this->once())->method('next');
        $reader = new DelegatorReader(array($internalReader1));
        $reader->open(new Resource());
        $reader->next();
    }

    /**
     * @covers Yosmanyga\Resource\Reader\Iterator\DelegatorReader::close
     */
    public function testClose()
    {
        $internalReader1 = $this->getMock('Yosmanyga\Resource\Reader\Iterator\ReaderInterface');
        $internalReader1->expects($this->once())->method('supports')->will($this->returnValue(true));
        $internalReader1->expects($this->once())->method('close');
        $reader = new DelegatorReader(array($internalReader1));
        $reader->open(new Resource());
        $reader->close();
    }

    /**
     * @covers Yosmanyga\Resource\Reader\Iterator\DelegatorReader::pickReader
     */
    public function testPickReader()
    {
        $r = new \ReflectionClass('Yosmanyga\Resource\Reader\Iterator\DelegatorReader');
        $m = $r->getMethod('pickReader');
        $m->setAccessible(true);
        $internalReader1 = $this->getMock('Yosmanyga\Resource\Reader\Iterator\ReaderInterface');
        $internalReader2 = $this->getMock('Yosmanyga\Resource\Reader\Iterator\ReaderInterface');
        $internalReader3 = $this->getMock('Yosmanyga\Resource\Reader\Iterator\ReaderInterface');
        $internalReader2->expects($this->once())->method('supports')->will($this->returnValue(true));
        $reader = new DelegatorReader(array($internalReader1, $internalReader2, $internalReader3));
        $this->assertEquals($internalReader2, $m->invoke($reader, new Resource()));
        $p = $r->getProperty('readers');
        $p->setAccessible(true);
        $this->assertEquals(array(0 => $internalReader2, 1 => $internalReader1, 2 => $internalReader3), $p->getValue($reader));
    }

    /**
     * @covers Yosmanyga\Resource\Reader\Iterator\DelegatorReader::pickReader
     * @expectedException \RuntimeException
     */
    public function testPickReaderThrowsExceptionWithNoValidReader()
    {
        $r = new \ReflectionClass('Yosmanyga\Resource\Reader\Iterator\DelegatorReader');
        $m = $r->getMethod('pickReader');
        $m->setAccessible(true);
        $internalReader1 = $this->getMock('Yosmanyga\Resource\Reader\Iterator\ReaderInterface');
        $internalReader1->expects($this->once())->method('supports')->will($this->returnValue(false));
        $reader = new DelegatorReader(array($internalReader1));
        $m->invoke($reader, new Resource());
    }
}
