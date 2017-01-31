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
        $this->assertTrue($reader->supports(new Resource([], 'in_memory')));
        // Wrong type
        $this->assertFalse($reader->supports(new Resource([], 'foo')));
        // No type and data metadata
        $this->assertTrue($reader->supports(new Resource(['data' => []])));
        // No type and no data metadata
        $this->assertFalse($reader->supports(new Resource([])));
    }

    /**
     * @covers Yosmanyga\Resource\Reader\Iterator\InMemoryReader::open
     */
    public function testOpen()
    {
        $data = ['foo1' => 'bar1', 'foo2' => 'bar2'];
        $resource = new Resource(['data' => $data], 'in_memory');
        $reader = new InMemoryReader();

        $reader->open($resource);
        $this->assertEquals(['key' => 'foo1', 'value' => 'bar1'], $reader->current());
    }

    /**
     * @covers Yosmanyga\Resource\Reader\Iterator\InMemoryReader::open
     * @expectedException \InvalidArgumentException
     */
    public function testOpenThrowsExceptionWithInvalidDataMetadata()
    {
        $reader = new InMemoryReader();
        $reader->open(new Resource(['data' => 'foo']));
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

        $reader->open(new Resource(['data' => ['foo1' => 'bar1', 'foo2' => 'bar2']], 'in_memory'));
        $reader->next();
        $this->assertEquals(['key' => 'foo2', 'value' => 'bar2'], $reader->current());
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
        $reader->open(new Resource(['data' => []], 'in_memory'));
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
