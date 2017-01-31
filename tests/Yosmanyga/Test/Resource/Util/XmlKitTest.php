<?php

namespace Yosmanyga\Test\Resource\Util;

use Yosmanyga\Resource\Util\XmlKit;

class XmlKitTest extends \PHPUnit_Framework_TestCase
{
    public function testNormalize()
    {
        $xmlNodeNormalizer = new XmlKit();

        $this->assertEquals(
            [
                'tag1' => 'value1',
            ],
            $xmlNodeNormalizer->extractContent(
                [
                    'name'  => 'tag1',
                    'value' => 'value1',
                ]
            )
        );

        $this->assertEquals(
            [
                'tag1' => 'value1',
                'tag2' => 'value2',
            ],
            $xmlNodeNormalizer->extractContent(
                [
                    [
                        'name'  => 'tag1',
                        'value' => 'value1',
                    ],
                    [
                        'name'  => 'tag2',
                        'value' => 'value2',
                    ],
                ]
            )
        );
    }
}
