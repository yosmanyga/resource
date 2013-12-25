<?php

namespace Yosmanyga\Test\Resource\Reader\Flat;

use Yosmanyga\Resource\Reader\Flat\DelegatorReader;
use Yosmanyga\Resource\Reader\Flat\FileReader;
use Yosmanyga\Resource\Resource;

class DelegatorReaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Yosmanyga\Resource\Reader\Flat\DelegatorReader::__construct
     */
    public function testConstructor()
    {
        $delegatorReader = new DelegatorReader();
        $this->assertAttributeEquals(
            array(
                new FileReader()
            ),
            'readers',
            $delegatorReader
        );

        $internalReader1 = $this->getMock('Yosmanyga\Resource\Reader\Flat\ReaderInterface');
        $delegatorReader = new DelegatorReader(array($internalReader1));
        $this->assertAttributeEquals(array($internalReader1), 'readers', $delegatorReader);
    }

    /**
     * @covers Yosmanyga\Resource\Reader\Flat\DelegatorReader::supports
     */
    public function testSupports()
    {
        $internalReader1 = $this->getMock('Yosmanyga\Resource\Reader\Flat\ReaderInterface');
        $internalReader2 = $this->getMock('Yosmanyga\Resource\Reader\Flat\ReaderInterface');
        $internalReader3 = $this->getMock('Yosmanyga\Resource\Reader\Flat\ReaderInterface');
        $internalReader2->expects($this->once())->method('supports')->will($this->returnValue(true));
        $reader = new DelegatorReader(array($internalReader1, $internalReader2, $internalReader3));
        $this->assertTrue($reader->supports(new Resource()));
        $this->assertAttributeEquals(array(0 => $internalReader2, 1 => $internalReader1, 3 => $internalReader3), 'readers', $reader);

        $internalReader1 = $this->getMock('Yosmanyga\Resource\Reader\Flat\ReaderInterface');
        $internalReader1->expects($this->once())->method('supports')->will($this->returnValue(false));
        $reader = new DelegatorReader(array($internalReader1));
        $this->assertFalse($reader->supports(new Resource()));
    }

    /**
     * @covers Yosmanyga\Resource\Reader\Flat\DelegatorReader::read
     */
    public function testRead()
    {
        $internalReader1 = $this->getMock('Yosmanyga\Resource\Reader\Flat\ReaderInterface');
        $internalReader1->expects($this->once())->method('supports')->will($this->returnValue(true));
        $internalReader1->expects($this->once())->method('read');
        $delegatorReader = new DelegatorReader(array($internalReader1));
        $delegatorReader->read(new Resource());
    }

    /**
     * @covers Yosmanyga\Resource\Reader\Flat\DelegatorReader::pickReader
     */
    public function testPickReader()
    {
        $r = new \ReflectionClass('Yosmanyga\Resource\Reader\Flat\DelegatorReader');
        $m = $r->getMethod('pickReader');
        $m->setAccessible(true);
        $internalReader1 = $this->getMock('Yosmanyga\Resource\Reader\Flat\ReaderInterface');
        $internalReader2 = $this->getMock('Yosmanyga\Resource\Reader\Flat\ReaderInterface');
        $internalReader3 = $this->getMock('Yosmanyga\Resource\Reader\Flat\ReaderInterface');
        $internalReader2->expects($this->once())->method('supports')->will($this->returnValue(true));
        $reader = new DelegatorReader(array($internalReader1, $internalReader2, $internalReader3));
        $this->assertEquals($internalReader2, $m->invoke($reader, new Resource()));
        $this->assertAttributeEquals(array(0 => $internalReader2, 1 => $internalReader1, 3 => $internalReader3), 'readers', $reader);
    }

    /**
     * @covers Yosmanyga\Resource\Reader\Flat\DelegatorReader::pickReader
     * @expectedException \RuntimeException
     */
    public function testPickReaderThrowsExceptionWithNoValidReader()
    {
        $r = new \ReflectionClass('Yosmanyga\Resource\Reader\Flat\DelegatorReader');
        $m = $r->getMethod('pickReader');
        $m->setAccessible(true);
        $internalReader1 = $this->getMock('Yosmanyga\Resource\Reader\Flat\ReaderInterface');
        $internalReader1->expects($this->once())->method('supports')->will($this->returnValue(false));
        $delegatorReader = new DelegatorReader(array($internalReader1));
        $m->invoke($delegatorReader, new Resource());
    }
}
