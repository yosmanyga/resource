<?php

namespace Yosmanyga\Test\Resource\Compiler\Iterator;

use Yosmanyga\Resource\Compiler\DelegatorCompiler;
use Yosmanyga\Resource\Resource;

class DelegatorCompilerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Yosmanyga\Resource\Compiler\DelegatorCompiler::__construct
     */
    public function testConstructor()
    {
        $internalCompiler1 = $this->getMock('Yosmanyga\Resource\Compiler\CompilerInterface');
        $compiler = new DelegatorCompiler([$internalCompiler1]);
        $this->assertAttributeEquals([$internalCompiler1], 'compilers', $compiler);
    }

    /**
     * @covers Yosmanyga\Resource\Compiler\DelegatorCompiler::supports
     */
    public function testSupports()
    {
        $definition = 'foo';

        $compiler = $this->getMock('Yosmanyga\Resource\Compiler\DelegatorCompiler', ['pickCompiler']);
        $compiler->expects($this->once())->method('pickCompiler')->with($definition)->will($this->returnValue(true));
        $this->assertTrue($compiler->supports($definition));

        $compiler = $this->getMock('Yosmanyga\Resource\Compiler\DelegatorCompiler', ['pickCompiler']);
        $compiler->expects($this->once())->method('pickCompiler')->with($definition)->will($this->returnValue(false));
        $this->assertFalse($compiler->supports($definition));
    }

    /**
     * @covers Yosmanyga\Resource\Compiler\DelegatorCompiler::compile
     */
    public function testCurrent()
    {
        $internalCompiler1 = $this->getMock('Yosmanyga\Resource\Compiler\CompilerInterface');
        $internalCompiler1->expects($this->once())->method('supports')->will($this->returnValue(true));
        $internalCompiler1->expects($this->once())->method('compile')->with('foo');
        $compiler = new DelegatorCompiler([$internalCompiler1]);
        $compiler->compile('foo');
    }

    /**
     * @covers Yosmanyga\Resource\Compiler\DelegatorCompiler::pickCompiler
     */
    public function testPickCompiler()
    {
        $r = new \ReflectionClass('Yosmanyga\Resource\Compiler\DelegatorCompiler');
        $m = $r->getMethod('pickCompiler');
        $m->setAccessible(true);
        $internalCompiler1 = $this->getMock('Yosmanyga\Resource\Compiler\CompilerInterface');
        $internalCompiler2 = $this->getMock('Yosmanyga\Resource\Compiler\CompilerInterface');
        $internalCompiler3 = $this->getMock('Yosmanyga\Resource\Compiler\CompilerInterface');
        $internalCompiler2->expects($this->once())->method('supports')->will($this->returnValue(true));
        $compiler = new DelegatorCompiler([$internalCompiler1, $internalCompiler2, $internalCompiler3]);
        $this->assertEquals($internalCompiler2, $m->invoke($compiler, new Resource()));
        $p = $r->getProperty('compilers');
        $p->setAccessible(true);
        $this->assertEquals([0 => $internalCompiler2, 1 => $internalCompiler1, 2 => $internalCompiler3], $p->getValue($compiler));
    }

    /**
     * @covers Yosmanyga\Resource\Compiler\DelegatorCompiler::pickCompiler
     * @expectedException \RuntimeException
     */
    public function testPickCompilerThrowsExceptionWithNoValidCompiler()
    {
        $r = new \ReflectionClass('Yosmanyga\Resource\Compiler\DelegatorCompiler');
        $m = $r->getMethod('pickCompiler');
        $m->setAccessible(true);
        $internalCompiler1 = $this->getMock('Yosmanyga\Resource\Compiler\CompilerInterface');
        $internalCompiler1->expects($this->once())->method('supports')->will($this->returnValue(false));
        $compiler = new DelegatorCompiler([$internalCompiler1]);
        $m->invoke($compiler, new Resource());
    }
}
