<?php

namespace Yosmanyga\Test\Resource\Reader\Iterator;

use Yosmanyga\Resource\Reader\Iterator\SuddenAnnotationFileReader;
use Yosmanyga\Resource\Resource;

class SuddenAnnotationFileReaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Yosmanyga\Resource\Reader\Iterator\SuddenAnnotationFileReader::open
     */
    public function testOpen()
    {
        $resource = new Resource(array('file' => sprintf("%s/Fixtures/Foo.php", dirname(__FILE__)), 'annotation' => '/AnnotationX/'));
        $reader = new SuddenAnnotationFileReader();

        $reader->open($resource);
        $this->assertEquals(
            array(
                'key' => 0,
                'value' => array(
                    0 => array(
                        'class' => 'Yosmanyga\Test\Resource\Reader\Iterator\Fixtures\Foo',
                        'key' => 'AnnotationX',
                        'value' => array(
                            'foo11' => 'bar11',
                            'foo12' => 'bar12'
                        ),
                        'metadata' => array(
                            'class' => 'Yosmanyga\Test\Resource\Reader\Iterator\Fixtures\Foo'
                        )
                    ),
                    1 => array(
                        'method' => 'bar',
                        'key' => 'AnnotationX',
                        'value' => array(
                            'foo21' => 'bar21',
                            'foo22' => 'bar22'
                        ),
                        'metadata' => array(
                            'class' => 'Yosmanyga\Test\Resource\Reader\Iterator\Fixtures\Foo'
                        )
                    ),
                )
            ),
            $reader->current()
        );
    }
}
