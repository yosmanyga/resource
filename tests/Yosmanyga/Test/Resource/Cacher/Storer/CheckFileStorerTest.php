<?php

namespace Yosmanyga\Test\Resource\Cacher\Storer;

use Yosmanyga\Resource\Cacher\Storer\CheckFileStorer;
use Yosmanyga\Resource\Resource;

class CheckFileStorerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Yosmanyga\Resource\Cacher\Storer\CheckFileStorer::__construct
     */
    public function testConstructor()
    {
        $cacher = new CheckFileStorer();
        $this->assertAttributeEquals('.check', 'suffix', $cacher);

        $cacher = new CheckFileStorer('', '.meta');
        $this->assertAttributeEquals('.meta', 'suffix', $cacher);
    }

    /**
     * @covers Yosmanyga\Resource\Cacher\Storer\CheckFileStorer::getFilename
     */
    public function testGetFilename()
    {
        $storer = new CheckFileStorer('foo', '.meta');
        $m = new \ReflectionMethod($storer, 'getFilename');
        $m->setAccessible(true);
        $resource = new Resource();
        $filename = sprintf('foo/%s.meta', md5(serialize($resource)));
        $this->assertEquals($filename, $m->invoke($storer, new Resource()));
    }
}
