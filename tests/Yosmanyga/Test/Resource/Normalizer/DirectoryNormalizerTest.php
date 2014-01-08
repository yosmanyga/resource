<?php

namespace Yosmanyga\Test\Resource\Normalizer;

use Yosmanyga\Resource\Normalizer\DirectoryNormalizer;
use Yosmanyga\Resource\Resource;

class DirectoryNormalizerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Yosmanyga\Resource\Normalizer\DirectoryNormalizer::supports
     */
    public function testSupports()
    {
        $resource = new Resource(array('dir' => '/foo', 'type' => 'foo'));

        $normalizer = new DirectoryNormalizer();
        // No dir metadata
        $this->assertFalse($normalizer->supports('', new Resource(array('type' => 'foo'))));
        // No type metadata
        $this->assertFalse($normalizer->supports('', new Resource(array('dir' => '/foo'))));

        $internalNormalizer1 = $this->getMock('Yosmanyga\Resource\Normalizer\NormalizerInterface');
        $internalNormalizer1->expects($this->once())->method('supports')->will($this->returnValue(true));
        $normalizer = new DirectoryNormalizer(array($internalNormalizer1));
        $this->assertTrue($normalizer->supports(array(), $resource));
        $internalNormalizer1 = $this->getMock('Yosmanyga\Resource\Normalizer\NormalizerInterface');
        $internalNormalizer1->expects($this->once())->method('supports')->will($this->returnValue(false));
        $normalizer = new DirectoryNormalizer(array($internalNormalizer1));
        $this->assertFalse($normalizer->supports(array(), $resource));
    }

    /**
     * @covers Yosmanyga\Resource\Normalizer\DirectoryNormalizer::normalize
     */
    public function testNormalize()
    {
        $internalNormalizer1 = $this->getMock('Yosmanyga\Resource\Normalizer\NormalizerInterface');
        $internalNormalizer1->expects($this->once())->method('supports')->will($this->returnValue(true));
        $internalNormalizer1->expects($this->once())->method('normalize');
        $normalizer = new DirectoryNormalizer(array($internalNormalizer1));
        $normalizer->normalize(array(), new Resource(array('type' => 'foo')));
    }

    /**
     * @covers Yosmanyga\Resource\Normalizer\DirectoryNormalizer::convertResource
     */
    public function testConvertResource()
    {
        $normalizer = new DirectoryNormalizer();
        $r = new \ReflectionClass('Yosmanyga\Resource\Normalizer\DirectoryNormalizer');
        $m = $r->getMethod('convertResource');
        $m->setAccessible(true);
        $resource = new Resource(array('dir' => '/foo', 'filter' => '*.php', 'type' => 'annotation'));
        $this->assertEquals(
            new Resource(array('dir' => '/foo', 'filter' => '*.php', 'type' => 'annotation'), 'annotation'),
            $m->invoke($normalizer, $resource)
        );
    }
}
