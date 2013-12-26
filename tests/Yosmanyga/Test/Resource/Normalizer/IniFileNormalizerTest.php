<?php

namespace Yosmanyga\Test\Resource\Normalizer;

use Yosmanyga\Resource\Normalizer\IniFileNormalizer;
use Yosmanyga\Resource\Normalizer\DelegatorNormalizer;
use Yosmanyga\Resource\Resource;

class IniFileNormalizerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Yosmanyga\Resource\Normalizer\IniFileNormalizer::__construct
     */
    public function testConstructor()
    {
        $directoryNormalizer = new IniFileNormalizer();
        $this->assertAttributeEquals(new DelegatorNormalizer(array()), 'normalizer', $directoryNormalizer);

        $internalNormalizer1 = $this->getMock('Yosmanyga\Resource\Normalizer\NormalizerInterface');
        $directoryNormalizer = new IniFileNormalizer(array($internalNormalizer1));
        $this->assertAttributeEquals(new DelegatorNormalizer(array($internalNormalizer1)), 'normalizer', $directoryNormalizer);
    }

    /**
     * @covers Yosmanyga\Resource\Normalizer\IniFileNormalizer::supports
     */
    public function testSupports()
    {
        $normalizer = new IniFileNormalizer();

        // Right type
        $this->assertTrue($normalizer->supports(null, new Resource(array(), 'ini')));
        // Wrong type
        $this->assertFalse($normalizer->supports(null, new Resource(array(), 'foo')));
        // No type, file metadata and right extension
        $extensions = array('ini');
        foreach ($extensions as $extension) {
            $this->assertTrue($normalizer->supports(null, new Resource(array('file' => "foo.$extension"))));
        }
        // No type, file metadata and wrong extension
        $this->assertFalse($normalizer->supports(null, new Resource(array('file' => 'foo.bar'))));
    }

    /**
     * @covers Yosmanyga\Resource\Normalizer\IniFileNormalizer::normalize
     */
    public function testNormalize()
    {
        $delegatorNormalizer = $this->getMock('Yosmanyga\Resource\Normalizer\DelegatorNormalizer');
        $normalizer = new IniFileNormalizer();
        $p = new \ReflectionProperty($normalizer, 'normalizer');
        $p->setAccessible(true);
        $p->setValue($normalizer, $delegatorNormalizer);
        $delegatorNormalizer->expects($this->once())->method('normalize');
        $normalizer->normalize(null, new Resource());
    }
}
