<?php

namespace Yosmanyga\Test\Resource\Reader\Iterator;

use Yosmanyga\Resource\Reader\Iterator\XmlFileReader;
use Yosmanyga\Resource\Resource;
use Yosmanyga\Resource\Util\XmlKit;

class XmlFileReaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Yosmanyga\Resource\Reader\Iterator\DirectoryReader::__construct
     */
    public function testConstructor()
    {
        $reader = new XmlFileReader();
        $this->assertAttributeEquals(
            new XmlKit(),
            'xmlKit',
            $reader
        );

        $xmlKit = $this->getMock('Yosmanyga\Resource\Util\XmlKit');
        $reader = new XmlFileReader($xmlKit);
        $this->assertAttributeEquals(
            $xmlKit,
            'xmlKit',
            $reader
        );
    }

    /**
     * @covers Yosmanyga\Resource\Reader\Iterator\XmlFileReader::supports
     */
    public function testSupports()
    {
        $reader = new XmlFileReader();

        // Right type
        $this->assertTrue($reader->supports(new Resource(array(), 'xml')));
        // Wrong type
        $this->assertFalse($reader->supports(new Resource(array(), 'foo')));
        // No type, file metadata and right extension
        $extensions = array('xml');
        foreach ($extensions as $extension) {
            $this->assertTrue($reader->supports(new Resource(array('file' => "foo.$extension"))));
        }
        // No type, file metadata and wrong extension
        $this->assertFalse($reader->supports(new Resource(array('file' => 'foo.bar'))));
    }

    /**
     * @covers Yosmanyga\Resource\Reader\Iterator\XmlFileReader::open
     */
    public function testOpen()
    {
        $resource = new Resource(array('file' => sprintf("%s/Fixtures/foo.xml", dirname(__FILE__))));
        $reader = new XmlFileReader();

        $reader->open($resource);
        $this->assertEquals(array('value' => array('id' => 'foo1', 'foo11' => 'bar11', 'foo12' => 'bar12')), $reader->current());
    }

    /**
     * @covers Yosmanyga\Resource\Reader\Iterator\XmlFileReader::open
     * @expectedException \InvalidArgumentException
     */
    public function testOpenThrowsExceptionWithNotFoundFileMetadata()
    {
        $reader = new XmlFileReader();
        $reader->open(new Resource(array('file' => '')));
    }

    /**
     * @covers Yosmanyga\Resource\Reader\Iterator\XmlFileReader::open
     * @expectedException \InvalidArgumentException
     */
    public function testOpenThrowsExceptionWithInvalidFileMetadata()
    {
        $reader = new XmlFileReader();
        $reader->open(new Resource(array('file' => sprintf("%s/Fixtures/foo_invalid.xml", dirname(__FILE__)))));
    }

    /**
     * @covers Yosmanyga\Resource\Reader\Iterator\XmlFileReader::current
     * @covers Yosmanyga\Resource\Reader\Iterator\XmlFileReader::next
     */
    public function testNext()
    {
        $reader = new XmlFileReader();

        $reader->open(new Resource(array('file' => sprintf("%s/Fixtures/foo.xml", dirname(__FILE__)))));
        $reader->next();
        $this->assertEquals(array('value' => array('id' => 'foo2', 'foo21' => 'bar21', 'foo22' => 'bar22')), $reader->current());
        $reader->next();
        $this->assertFalse($reader->current());
    }

    /**
     * @covers Yosmanyga\Resource\Reader\Iterator\XmlFileReader::current
     * @expectedException \RuntimeException
     */
    public function testCurrentThrowsExceptionWhenNoOpenResource()
    {
        $reader = new XmlFileReader();
        $reader->current();
    }

    /**
     * @covers Yosmanyga\Resource\Reader\Iterator\XmlFileReader::next
     * @expectedException \RuntimeException
     */
    public function testNextThrowsExceptionWhenNoOpenResource()
    {
        $reader = new XmlFileReader();
        $reader->next();
    }

    /**
     * @covers Yosmanyga\Resource\Reader\Iterator\XmlFileReader::close
     * @expectedException \RuntimeException
     */
    public function testClose()
    {
        $reader = new XmlFileReader();
        $reader->open(new Resource(array('file' => sprintf("%s/Fixtures/foo.xml", dirname(__FILE__)))));
        $reader->close();
        $reader->current();
    }

    /**
     * @covers Yosmanyga\Resource\Reader\Iterator\XmlFileReader::close
     * @expectedException \RuntimeException
     */
    public function testCloseThrowsExceptionWhenNoOpenResource()
    {
        $reader = new XmlFileReader();
        $reader->close();
    }
}
