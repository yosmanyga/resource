<?php

namespace Yosmanyga\Test\Resource\Normalizer;

use Yosmanyga\Resource\Normalizer\XmlFileDelegatorNormalizer;
use Yosmanyga\Resource\Resource;

class XmlFileDelegatorNormalizerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Yosmanyga\Resource\Normalizer\XmlFileDelegatorNormalizer::supports
     */
    public function testSupports()
    {
        $normalizer = new XmlFileDelegatorNormalizer();

        // Right type
        $this->assertTrue($normalizer->supports(null, new Resource(array(), 'xml')));
        // Wrong type
        $this->assertFalse($normalizer->supports(null, new Resource(array(), 'foo')));
        // No type, file metadata and right extension
        $extensions = array('xml');
        foreach ($extensions as $extension) {
            $this->assertTrue($normalizer->supports(null, new Resource(array('file' => "foo.$extension"))));
        }
        // No type, file metadata and wrong extension
        $this->assertFalse($normalizer->supports(null, new Resource(array('file' => 'foo.bar'))));
    }
}
