<?php

namespace Yosmanyga\Test\Resource\Reader\Iterator;

use Yosmanyga\Resource\Reader\Iterator\InMemoryReader;
use Yosmanyga\Resource\Resource;

class InMemoryReaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Yosmanyga\Resource\Reader\Iterator\InMemoryReader::supports
     */
    public function testSupports()
    {
        $reader = new InMemoryReader();

        // Right type
        $this->assertTrue($reader->supports(new Resource(array(), 'in_memory')));
        // Wrong type
        $this->assertFalse($reader->supports(new Resource(array(), 'foo')));
        // No type and data metadata
        $this->assertTrue($reader->supports(new Resource(array('data' => array()))));
        // No type and no data metadata
        $this->assertFalse($reader->supports(new Resource(array())));
    }

    /**
     * @covers Yosmanyga\Resource\Reader\Iterator\InMemoryReader::open
     */
    public function testOpen()
    {
        $data = array('foo1' => 'bar1', 'foo2' => 'bar2');
        $resource = new Resource(array('data' => $data), 'in_memory');
        $reader = new InMemoryReader();

        $reader->open($resource);
        $this->assertEquals(array('key' => 'foo1', 'value' => 'bar1'), $reader->current());
    }

    /**
     * @covers Yosmanyga\Resource\Reader\Iterator\InMemoryReader::open
     * @expectedException \InvalidArgumentException
     */
    public function testOpenThrowsExceptionWithInvalidDataMetadata()
    {
        $reader = new InMemoryReader();
        $reader->open(new Resource(array('data' => 'foo')));
    }

    /**
     * @covers Yosmanyga\Resource\Reader\Iterator\InMemoryReader::current
     * @expectedException \RuntimeException
     */
    public function testCurrentThrowsExceptionWithInvalidDataMetadata()
    {
        $reader = new InMemoryReader();
        $reader->current();
    }

    /**
     * @covers Yosmanyga\Resource\Reader\Iterator\InMemoryReader::current
     * @covers Yosmanyga\Resource\Reader\Iterator\InMemoryReader::next
     */
    public function testNext()
    {
        $reader = new InMemoryReader();

        $reader->open(new Resource(array('data' => array('foo1' => 'bar1', 'foo2' => 'bar2')), 'in_memory'));
        $reader->next();
        $this->assertEquals(array('key' => 'foo2', 'value' => 'bar2'), $reader->current());
        $reader->next();
        $this->assertFalse($reader->current());
    }

    /**
     * @covers Yosmanyga\Resource\Reader\Iterator\InMemoryReader::current
     * @expectedException \RuntimeException
     */
    public function testCurrentThrowsExceptionWhenNoOpenResource()
    {
        $reader = new InMemoryReader();
        $reader->current();
    }

    /**
     * @covers Yosmanyga\Resource\Reader\Iterator\InMemoryReader::next
     * @expectedException \RuntimeException
     */
    public function testNextThrowsExceptionWhenNoOpenResource()
    {
        $reader = new InMemoryReader();
        $reader->next();
    }

    /**
     * @covers Yosmanyga\Resource\Reader\Iterator\InMemoryReader::close
     * @expectedException \RuntimeException
     */
    public function testClose()
    {
        $reader = new InMemoryReader();
        $reader->open(new Resource(array('data' => array()), 'in_memory'));
        $reader->close();
        $reader->current();
    }

    /**
     * @covers Yosmanyga\Resource\Reader\Iterator\InMemoryReader::close
     * @expectedException \RuntimeException
     */
    public function testCloseThrowsExceptionWhenNoOpenResource()
    {
        $reader = new InMemoryReader();
        $reader->close();
    }
}
