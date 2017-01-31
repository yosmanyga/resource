<?php

namespace Yosmanyga\Test\Resource\Util;

use Yosmanyga\Resource\Util\DocParser;

class DocParserTest extends \PHPUnit_Framework_TestCase
{
    public function testParse()
    {
        $docParser = new DocParser();
        $this->assertEquals(
            [
                0 => [
                    'class' => 'Yosmanyga\Test\Resource\Util\Fixtures\Foo',
                    'key'   => 'AnnotationX',
                    'value' => [
                        'foo11' => 'bar11',
                        'foo12' => 'bar12',
                    ],
                    'metadata' => [
                        'class' => 'Yosmanyga\Test\Resource\Util\Fixtures\Foo',
                    ],
                ],
                1 => [
                    'property' => 'p',
                    'key'      => 'AnnotationY',
                    'value'    => [
                        'foo1' => 'bar1',
                        'foo2' => 'bar2',
                    ],
                    'metadata' => [
                        'class' => 'Yosmanyga\Test\Resource\Util\Fixtures\Foo',
                    ],
                ],
                2 => [
                    'property' => 'p',
                    'key'      => 'AnnotationX',
                    'value'    => [],
                    'metadata' => [
                        'class' => 'Yosmanyga\Test\Resource\Util\Fixtures\Foo',
                    ],
                ],
                3 => [
                    'method' => 'bar',
                    'key'    => 'AnnotationX',
                    'value'  => [
                        'foo21' => 'bar21',
                        'foo22' => 'bar22',
                    ],
                    'metadata' => [
                        'class' => 'Yosmanyga\Test\Resource\Util\Fixtures\Foo',
                    ],
                ],
                4 => [
                    'method' => 'bar',
                    'key'    => 'AnnotationZ\Foo',
                    'value'  => [
                        'foo1' => 'bar1',
                        'foo2' => 'bar2',
                    ],
                    'metadata' => [
                        'class' => 'Yosmanyga\Test\Resource\Util\Fixtures\Foo',
                    ],
                ],
            ],
            $docParser->parse(sprintf('%s/Fixtures/Foo.php', dirname(__FILE__)))
        );

        $this->assertEquals(
            [
                0 => [
                    'method' => 'bar',
                    'key'    => 'AnnotationZ\Foo',
                    'value'  => [
                        'foo1' => 'bar1',
                        'foo2' => 'bar2',
                    ],
                    'metadata' => [
                        'class' => 'Yosmanyga\Test\Resource\Util\Fixtures\Foo',
                    ],
                ],
            ],
            $docParser->parse(sprintf('%s/Fixtures/Foo.php', dirname(__FILE__)), '/AnnotationZ\\\\Foo/')
        );
    }

    public function testResolveAnnotations()
    {
        $docParser = new DocParser();
        $m = new \ReflectionMethod('Yosmanyga\Resource\Util\DocParser', 'resolveAnnotations');
        $m->setAccessible(true);

        $data = <<<'EOF'
/**
 * @AnnotationX({
 *  foo1: 'bar1',
 *  foo2: 'bar2'
 * })
 * @AnnotationY({foo1: 'bar1', foo2: 'bar2'})
 */
EOF;
        $this->assertEquals(
            [
                0 => [
                    'key'   => 'AnnotationX',
                    'value' => "{ foo1: 'bar1', foo2: 'bar2'}",
                ],
                1 => [
                    'key'   => 'AnnotationY',
                    'value' => "{foo1: 'bar1', foo2: 'bar2'}",
                ],
            ],
            $m->invoke($docParser, $data)
        );

        $docParser = new DocParser();
        $m = new \ReflectionMethod('Yosmanyga\Resource\Util\DocParser', 'resolveAnnotations');
        $m->setAccessible(true);

        $data = '';
        $this->assertEquals(
            [],
            $m->invoke($docParser, $data)
        );
    }

    public function testSplitAnnotations()
    {
        $docParser = new DocParser();
        $m = new \ReflectionMethod('Yosmanyga\Resource\Util\DocParser', 'splitAnnotations');
        $m->setAccessible(true);

        $data = "@AnnotationX({foo1: 'bar1', foo2: 'bar2'})\n@AnnotationY({foo1: 'bar1', foo2: 'bar2'})";
        $this->assertEquals(
            [
                0 => "@AnnotationX({foo1: 'bar1', foo2: 'bar2'})",
                1 => "@AnnotationY({foo1: 'bar1', foo2: 'bar2'})",
            ],
            $m->invoke($docParser, $data)
        );

        $data = "@AnnotationX({foo1: 'bar1', foo2: 'bar2'})";
        $this->assertEquals(
            [
                0 => "@AnnotationX({foo1: 'bar1', foo2: 'bar2'})",
            ],
            $m->invoke($docParser, $data)
        );
    }

    public function testCleanContents()
    {
        $docParser = new DocParser();
        $m = new \ReflectionMethod('Yosmanyga\Resource\Util\DocParser', 'cleanContents');
        $m->setAccessible(true);

        $data = [
            "@AnnotationX({\nfoo1: 'bar1',\nfoo2: 'bar2'\n})",
        ];
        $this->assertEquals(
            [
                0 => "@AnnotationX({foo1: 'bar1',foo2: 'bar2'})",
            ],
            $m->invoke($docParser, $data)
        );
    }

    public function testParseAnnotations()
    {
        $docParser = new DocParser();
        $m = new \ReflectionMethod('Yosmanyga\Resource\Util\DocParser', 'parseAnnotations');
        $m->setAccessible(true);

        $data = [
            "@AnnotationX({foo1: 'bar1',foo2: 'bar2'})",
        ];
        $this->assertEquals(
            [
                0 => [
                    'key'   => 'AnnotationX',
                    'value' => "{foo1: 'bar1',foo2: 'bar2'}",
                ],
            ],
            $m->invoke($docParser, $data)
        );

        $data = [
            '@AnnotationX',
        ];
        $this->assertEquals(
            [
                0 => [
                    'key'   => 'AnnotationX',
                    'value' => '',
                ],
            ],
            $m->invoke($docParser, $data)
        );
    }
}
