<?php

namespace Yosmanyga\Test\Resource\Normalizer;

use Yosmanyga\Resource\Normalizer\IniFileDelegatorNormalizer;
use Yosmanyga\Resource\Resource;

class IniFileNormalizerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Yosmanyga\Resource\Normalizer\IniFileDelegatorNormalizer::supports
     */
    public function testSupports()
    {
        $normalizer = new IniFileDelegatorNormalizer();

        // Right type
        $this->assertTrue($normalizer->supports(null, new Resource([], 'ini')));
        // Wrong type
        $this->assertFalse($normalizer->supports(null, new Resource([], 'foo')));
        // No type, file metadata and right extension
        $extensions = ['ini'];
        foreach ($extensions as $extension) {
            $this->assertTrue($normalizer->supports(null, new Resource(['file' => "foo.$extension"])));
        }
        // No type, file metadata and wrong extension
        $this->assertFalse($normalizer->supports(null, new Resource(['file' => 'foo.bar'])));
    }
}
