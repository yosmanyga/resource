<?php

namespace Yosmanyga\Test\Resource\Cacher\VersionChecker;

use Yosmanyga\Resource\Cacher\VersionChecker\FileVersionChecker;
use Yosmanyga\Resource\Resource;

class FileVersionCheckerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Yosmanyga\Resource\Cacher\VersionChecker\FileVersionChecker::supports
     */
    public function testSupports()
    {
        $versionChecker = new FileVersionChecker();

        // No file metadata
        $this->assertFalse($versionChecker->supports(new Resource(array())));
        // Right metadata
        $this->assertTrue($versionChecker->supports(new Resource(array('file' => 'foo'))));
    }

    /**
     * @covers Yosmanyga\Resource\Cacher\VersionChecker\FileVersionChecker::get
     */
    public function testGet()
    {
        $resource = new Resource(array('file' => __FILE__));
        $versionChecker = new FileVersionChecker();
        $this->assertEquals(filemtime(__FILE__), $versionChecker->get($resource));
    }
}
