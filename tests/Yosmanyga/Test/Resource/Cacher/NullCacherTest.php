<?php

namespace Yosmanyga\Test\Resource\Cacher;

use Yosmanyga\Resource\Cacher\NullCacher;
use Yosmanyga\Resource\Resource;

class NullCacherTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Yosmanyga\Resource\Cacher\NullCacher::store
     */
    public function testStore()
    {
        $cacher = new NullCacher();
        $cacher->store('', new Resource());
    }

    /**
     * @covers Yosmanyga\Resource\Cacher\NullCacher::retrieve
     */
    public function testRetrieve()
    {
        $cacher = new NullCacher();
        $this->assertNull($cacher->retrieve(new Resource()));
    }

    /**
     * @covers Yosmanyga\Resource\Cacher\NullCacher::check
     */
    public function testCheck()
    {
        $cacher = new NullCacher();
        $this->assertFalse($cacher->check(new Resource()));
    }
}
