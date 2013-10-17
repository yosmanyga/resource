<?php

namespace Yosmanyga\Test\Resource\Reader\Iterator;

use Yosmanyga\Resource\Reader\Iterator\YamlFileReader;
use Yosmanyga\Resource\Resource;

class YamlFileReaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Yosmanyga\Resource\Reader\Iterator\YamlFileReader::supports
     */
    public function testSupports()
    {
        $reader = new YamlFileReader();

        // Right type
        $this->assertTrue($reader->supports(new Resource(array(), 'yaml')));
        // Wrong type
        $this->assertFalse($reader->supports(new Resource(array(), 'foo')));
        // No type, file metadata and right extension
        $extensions = array('yaml', 'yml');
        foreach ($extensions as $extension) {
            $this->assertTrue($reader->supports(new Resource(array('file' => "foo.$extension"))));
        }
        // No type, file metadata and wrong extension
        $this->assertFalse($reader->supports(new Resource(array('file' => 'foo.bar'))));
    }

    /**
     * @covers Yosmanyga\Resource\Reader\Iterator\YamlFileReader::open
     */
    public function testOpen()
    {
        $resource = new Resource(array('file' => sprintf("%s/Fixtures/foo.yml", dirname(__FILE__))));
        $reader = new YamlFileReader();

        $reader->open($resource);
        $this->assertEquals(array('key' => 'foo1', 'value' => array('foo11' => 'bar11', 'foo12' => 'bar12')), $reader->current());
    }

    /**
     * @covers Yosmanyga\Resource\Reader\Iterator\YamlFileReader::open
     * @expectedException \InvalidArgumentException
     */
    public function testOpenThrowsExceptionWithNotFoundFileMetadata()
    {
        $reader = new YamlFileReader();
        $reader->open(new Resource(array('file' => '')));
    }

    /**
     * @covers Yosmanyga\Resource\Reader\Iterator\YamlFileReader::open
     * @expectedException \InvalidArgumentException
     */
    public function testOpenThrowsExceptionWithInvalidFileMetadata()
    {
        $reader = new YamlFileReader();
        $reader->open(new Resource(array('file' => sprintf("%s/Fixtures/foo_invalid.yml", dirname(__FILE__)))));
    }

    /**
     * @covers Yosmanyga\Resource\Reader\Iterator\YamlFileReader::current
     * @covers Yosmanyga\Resource\Reader\Iterator\YamlFileReader::next
     */
    public function testNext()
    {
        $reader = new YamlFileReader();

        $reader->open(new Resource(array('file' => sprintf("%s/Fixtures/foo.yml", dirname(__FILE__)))));
        $reader->next();
        $this->assertEquals(array('key' => 'foo2', 'value' => array('foo21' => 'bar21', 'foo22' => 'bar22')), $reader->current());
        $reader->next();
        $this->assertFalse($reader->current());
    }

    /**
     * @covers Yosmanyga\Resource\Reader\Iterator\YamlFileReader::current
     * @expectedException \RuntimeException
     */
    public function testCurrentThrowsExceptionWhenNoOpenResource()
    {
        $reader = new YamlFileReader();
        $reader->current();
    }

    /**
     * @covers Yosmanyga\Resource\Reader\Iterator\YamlFileReader::next
     * @expectedException \RuntimeException
     */
    public function testNextThrowsExceptionWhenNoOpenResource()
    {
        $reader = new YamlFileReader();
        $reader->next();
    }

    /**
     * @covers Yosmanyga\Resource\Reader\Iterator\YamlFileReader::close
     * @expectedException \RuntimeException
     */
    public function testClose()
    {
        $reader = new YamlFileReader();
        $reader->open(new Resource(array('file' => sprintf("%s/Fixtures/foo.yml", dirname(__FILE__)))));
        $reader->close();
        $reader->current();
    }

    /**
     * @covers Yosmanyga\Resource\Reader\Iterator\YamlFileReader::close
     * @expectedException \RuntimeException
     */
    public function testCloseThrowsExceptionWhenNoOpenResource()
    {
        $reader = new YamlFileReader();
        $reader->close();
    }
}
