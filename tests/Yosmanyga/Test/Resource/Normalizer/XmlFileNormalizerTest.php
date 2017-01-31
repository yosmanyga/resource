<?php

namespace Yosmanyga\Test\Resource\Normalizer;

use Yosmanyga\Resource\Normalizer\XmlFileDelegatorNormalizer;
use Yosmanyga\Resource\Resource;

class XmlFileNormalizerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Yosmanyga\Resource\Normalizer\XmlFileDelegatorNormalizer::supports
     */
    public function testSupports()
    {
        $normalizer = new XmlFileDelegatorNormalizer();

        // Right type
        $this->assertTrue($normalizer->supports(null, new Resource([], 'xml')));
        // Wrong type
        $this->assertFalse($normalizer->supports(null, new Resource([], 'foo')));
        // No type, file metadata and right extension
        $extensions = ['xml'];
        foreach ($extensions as $extension) {
            $this->assertTrue($normalizer->supports(null, new Resource(['file' => "foo.$extension"])));
        }
        // No type, file metadata and wrong extension
        $this->assertFalse($normalizer->supports(null, new Resource(['file' => 'foo.bar'])));
    }
}
