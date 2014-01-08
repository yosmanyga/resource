<?php

namespace Yosmanyga\Test\Resource\Normalizer;

use Yosmanyga\Resource\Normalizer\IniFileDelegatorNormalizer;
use Yosmanyga\Resource\Resource;

class IniFileDelegatorNormalizerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Yosmanyga\Resource\Normalizer\IniFileDelegatorNormalizer::supports
     */
    public function testSupports()
    {
        $normalizer = new IniFileDelegatorNormalizer();

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
}
