<?php

namespace Yosmanyga\Test\Resource\Normalizer;

use Yosmanyga\Resource\Normalizer\YamlFileDelegatorNormalizer;
use Yosmanyga\Resource\Resource;

class YamlFileDelegatorNormalizerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Yosmanyga\Resource\Normalizer\YamlFileDelegatorNormalizer::supports
     */
    public function testSupports()
    {
        $normalizer = new YamlFileDelegatorNormalizer();

        // Right type
        $this->assertTrue($normalizer->supports(null, new Resource(array(), 'yaml')));
        // Wrong type
        $this->assertFalse($normalizer->supports(null, new Resource(array(), 'foo')));
        // No type, file metadata and right extension
        $extensions = array('yaml', 'yml');
        foreach ($extensions as $extension) {
            $this->assertTrue($normalizer->supports(null, new Resource(array('file' => "foo.$extension"))));
        }
        // No type, file metadata and wrong extension
        $this->assertFalse($normalizer->supports(null, new Resource(array('file' => 'foo.bar'))));
    }
}
