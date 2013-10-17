<?php

namespace Yosmanyga\Test\Resource\Reader\Iterator;

use Yosmanyga\Resource\Reader\Iterator\IniFileReader;
use Yosmanyga\Resource\Resource;

class IniFileReaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Yosmanyga\Resource\Reader\Iterator\IniFileReader::supports
     */
    public function testSupports()
    {
        $reader = new IniFileReader();

        // Right type
        $this->assertTrue($reader->supports(new Resource(array(), 'ini')));
        // Wrong type
        $this->assertFalse($reader->supports(new Resource(array(), 'foo')));
        // No type, file metadata and right extension
        $extensions = array('ini');
        foreach ($extensions as $extension) {
            $this->assertTrue($reader->supports(new Resource(array('file' => "foo.$extension"))));
        }
        // No type, file metadata and wrong extension
        $this->assertFalse($reader->supports(new Resource(array('file' => 'foo.bar'))));
    }

    /**
     * @covers Yosmanyga\Resource\Reader\Iterator\IniFileReader::open
     */
    public function testOpen()
    {
        $resource = new Resource(array('file' => sprintf("%s/Fixtures/foo.ini", dirname(__FILE__))));
        $reader = new IniFileReader();

        $reader->open($resource);
        $this->assertEquals(array('key' => 'foo1', 'value' => array('foo11' => 'bar11', 'foo12' => 'bar12')), $reader->current());
    }

    /**
     * @covers Yosmanyga\Resource\Reader\Iterator\IniFileReader::open
     * @expectedException \InvalidArgumentException
     */
    public function testOpenThrowsExceptionWithNotFoundFileMetadata()
    {
        $reader = new IniFileReader();
        $reader->open(new Resource(array('file' => '')));
    }

    /**
     * @covers Yosmanyga\Resource\Reader\Iterator\IniFileReader::open
     * @expectedException \InvalidArgumentException
     */
    public function testOpenThrowsExceptionWithInvalidFileMetadata()
    {
        $reader = new IniFileReader();
        $reader->open(new Resource(array('file' => sprintf("%s/Fixtures/foo_invalid.ini", dirname(__FILE__)))));
    }

    /**
     * @covers Yosmanyga\Resource\Reader\Iterator\IniFileReader::current
     * @covers Yosmanyga\Resource\Reader\Iterator\IniFileReader::next
     */
    public function testNext()
    {
        $reader = new IniFileReader();

        $reader->open(new Resource(array('file' => sprintf("%s/Fixtures/foo.ini", dirname(__FILE__)))));
        $reader->next();
        $this->assertEquals(array('key' => 'foo2', 'value' => array('foo21' => 'bar21', 'foo22' => 'bar22')), $reader->current());
        $reader->next();
        $this->assertFalse($reader->current());
    }

    /**
     * @covers Yosmanyga\Resource\Reader\Iterator\IniFileReader::current
     * @expectedException \RuntimeException
     */
    public function testCurrentThrowsExceptionWhenNoOpenResource()
    {
        $reader = new IniFileReader();
        $reader->current();
    }

    /**
     * @covers Yosmanyga\Resource\Reader\Iterator\IniFileReader::next
     * @expectedException \RuntimeException
     */
    public function testNextThrowsExceptionWhenNoOpenResource()
    {
        $reader = new IniFileReader();
        $reader->next();
    }

    /**
     * @covers Yosmanyga\Resource\Reader\Iterator\IniFileReader::close
     * @expectedException \RuntimeException
     */
    public function testClose()
    {
        $reader = new IniFileReader();
        $reader->open(new Resource(array('file' => sprintf("%s/Fixtures/foo.ini", dirname(__FILE__)))));
        $reader->close();
        $reader->current();
    }

    /**
     * @covers Yosmanyga\Resource\Reader\Iterator\IniFileReader::close
     * @expectedException \RuntimeException
     */
    public function testCloseThrowsExceptionWhenNoOpenResource()
    {
        $reader = new IniFileReader();
        $reader->close();
    }
}
