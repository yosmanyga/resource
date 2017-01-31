<?php

namespace Yosmanyga\Test\Resource\Definition;

use Yosmanyga\Test\Resource\Definition\Fixtures\Definition;

class DefinitionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Yosmanyga\Resource\Definition\Definition::import
     */
    public function testImport()
    {
        $definition = new Definition();
        $definition->import(['foo1' => 'bar1']);
        $this->assertAttributeEquals('bar1', 'foo1', $definition);
    }

    /**
     * @covers Yosmanyga\Resource\Definition\Definition::export
     */
    public function testExport()
    {
        $definition = new Definition();
        $definition->foo2 = 'bar2';
        $this->assertEquals(['foo2' => 'bar2'], $definition->export());
    }

    /**
     * @covers Yosmanyga\Resource\Definition\Definition::validate
     * @expectedException \RuntimeException
     */
    public function testValidateThrowsExceptionWithInvalidData()
    {
        $definition = new Definition();
        $m = new \ReflectionMethod($definition, 'validate');
        $m->setAccessible(true);
        $m->invoke($definition, ['foo3']);
    }
}
