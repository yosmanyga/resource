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
        $resource = new Resource(['file' => sprintf('%s/Fixtures/Foo.php', dirname(__FILE__)), 'annotation' => '/AnnotationX/']);
        $reader = new SuddenAnnotationFileReader();

        $reader->open($resource);
        $this->assertEquals(
            [
                'key'   => 0,
                'value' => [
                    0 => [
                        'class' => 'Yosmanyga\Test\Resource\Reader\Iterator\Fixtures\Foo',
                        'key'   => 'AnnotationX',
                        'value' => [
                            'foo11' => 'bar11',
                            'foo12' => 'bar12',
                        ],
                        'metadata' => [
                            'class' => 'Yosmanyga\Test\Resource\Reader\Iterator\Fixtures\Foo',
                        ],
                    ],
                    1 => [
                        'method' => 'bar',
                        'key'    => 'AnnotationX',
                        'value'  => [
                            'foo21' => 'bar21',
                            'foo22' => 'bar22',
                        ],
                        'metadata' => [
                            'class' => 'Yosmanyga\Test\Resource\Reader\Iterator\Fixtures\Foo',
                        ],
                    ],
                ],
            ],
            $reader->current()
        );
    }

    /**
     * @covers Yosmanyga\Resource\Reader\Iterator\SuddenAnnotationFileReader::getData
     */
    public function testGetData()
    {
        $reader = new SuddenAnnotationFileReader();
        $docParser = $this->getMock('\Yosmanyga\Resource\Util\DocParserInterface');
        $p = new \ReflectionProperty($reader, 'docParser');
        $p->setAccessible(true);
        $p->setValue($reader, $docParser);
        $docParser->expects($this->once())->method('parse')->will($this->returnValue('foo'));
        $m = new \ReflectionMethod($reader, 'getData');
        $m->setAccessible(true);
        $this->assertEquals([0 => 'foo'], $m->invoke($reader, '', ''));

        $reader = new SuddenAnnotationFileReader();
        $docParser = $this->getMock('\Yosmanyga\Resource\Util\DocParserInterface');
        $p = new \ReflectionProperty($reader, 'docParser');
        $p->setAccessible(true);
        $p->setValue($reader, $docParser);
        $docParser->expects($this->once())->method('parse')->will($this->returnValue(false));
        $m = new \ReflectionMethod($reader, 'getData');
        $m->setAccessible(true);
        $this->assertEquals([], $m->invoke($reader, '', ''));
    }
}
