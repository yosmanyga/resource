<?php

namespace Yosmanyga\Test\Resource\Cacher\Storer;

use Yosmanyga\Resource\Cacher\Storer\FileStorer;
use Yosmanyga\Resource\Resource;

class FileStorerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Yosmanyga\Resource\Cacher\Storer\FileStorer::__construct
     */
    public function testConstructor()
    {
        $cacher = new FileStorer('/foo');
        $this->assertAttributeEquals('/foo', 'dir', $cacher);
        $this->assertAttributeEquals('', 'suffix', $cacher);

        $cacher = new FileStorer('/foo', '.meta');
        $this->assertAttributeEquals('.meta', 'suffix', $cacher);
    }

    /**
     * @covers Yosmanyga\Resource\Cacher\Storer\FileStorer::add
     * @covers Yosmanyga\Resource\Cacher\Storer\FileStorer::has
     * @covers Yosmanyga\Resource\Cacher\Storer\FileStorer::get
     */
    public function testAddHasGet()
    {
        $resource = new Resource();
        $cacher = new FileStorer(sys_get_temp_dir());
        $cacher->add('foo', $resource);
        $this->assertTrue($cacher->has($resource));
        $this->assertEquals('foo', $cacher->get($resource));
    }

    public function testGetFilenameThrowsExceptionWithNoValidReader()
    {
        $r = new \ReflectionClass('Yosmanyga\Resource\Cacher\Storer\FileStorer');
        $m = $r->getMethod('getFilename');
        $m->setAccessible(true);
        $storer = new FileStorer('foo', '.bar');
        $resource = new Resource();
        $filename = sprintf("foo/%s.bar", md5(serialize($resource)));
        $this->assertEquals($filename, $m->invoke($storer, new Resource()));
    }
}
