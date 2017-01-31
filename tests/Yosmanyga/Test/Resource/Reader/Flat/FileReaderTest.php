<?php

namespace Yosmanyga\Test\Resource\Reader\Flat;

use Yosmanyga\Resource\Reader\Flat\FileReader;
use Yosmanyga\Resource\Resource;

class FileReaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Yosmanyga\Resource\Reader\Flat\FileReader::supports
     */
    public function testSupports()
    {
        $reader = new FileReader();

        // Right type
        $this->assertTrue($reader->supports(new Resource([], 'file')));
        // Wrong type
        $this->assertFalse($reader->supports(new Resource([], 'foo')));
        // No type and file metadata
        $this->assertTrue($reader->supports(new Resource(['file' => '/foo.bar'])));
        // No type and no file metadata
        $this->assertFalse($reader->supports(new Resource([])));
    }

    /**
     * @covers Yosmanyga\Resource\Reader\Flat\FileReader::read
     */
    public function testRead()
    {
        $reader = new FileReader();
        $content = $reader->read(new Resource(['file' => sprintf('%s/Fixtures/foo.html', dirname(__FILE__))]));
        $this->assertEquals("foo\nbar", $content);
    }

    /**
     * @covers Yosmanyga\Resource\Reader\Flat\FileReader::read
     * @expectedException \InvalidArgumentException
     */
    public function testOpenThrowsExceptionWithInvalidFile()
    {
        $reader = new FileReader();
        $file = sprintf('%s/Fixtures/fuu.html', dirname(__FILE__));
        $reader->read(new Resource(['file' => $file]));
    }
}
