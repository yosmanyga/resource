<?php

namespace Yosmanyga\Test\Resource\Normalizer;

use Yosmanyga\Resource\Normalizer\YamlFileDelegatorNormalizer;
use Yosmanyga\Resource\Resource;

class YamlFileNormalizerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Yosmanyga\Resource\Normalizer\YamlFileDelegatorNormalizer::supports
     */
    public function testSupports()
    {
        $normalizer = new YamlFileDelegatorNormalizer();

        // Right type
        $this->assertTrue($normalizer->supports(null, new Resource([], 'yaml')));
        // Wrong type
        $this->assertFalse($normalizer->supports(null, new Resource([], 'foo')));
        // No type, file metadata and right extension
        $extensions = ['yaml', 'yml'];
        foreach ($extensions as $extension) {
            $this->assertTrue($normalizer->supports(null, new Resource(['file' => "foo.$extension"])));
        }
        // No type, file metadata and wrong extension
        $this->assertFalse($normalizer->supports(null, new Resource(['file' => 'foo.bar'])));
    }
}
