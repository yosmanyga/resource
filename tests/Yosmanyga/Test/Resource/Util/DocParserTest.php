<?php

namespace Yosmanyga\Test\Resource\Util;

use Yosmanyga\Resource\Util\DocParser;

class ResourceTest extends \PHPUnit_Framework_TestCase
{
    public function testParse()
    {
        $docParser = new DocParser();
        $this->assertEquals(
            array(
                0 => array(
                    'class' => 'Yosmanyga\Test\Resource\Util\Fixtures\Foo',
                    'key' => 'AnnotationX',
                    'value' => array(
                        'foo11' => 'bar11',
                        'foo12' => 'bar12'
                    ),
                    'metadata' => array(
                        'class' => 'Yosmanyga\Test\Resource\Util\Fixtures\Foo'
                    )
                ),
                1 => array(
                    'property' => 'p',
                    'key' => 'AnnotationY',
                    'value' => array(
                        'foo1' => 'bar1',
                        'foo2' => 'bar2'
                    ),
                    'metadata' => array(
                        'class' => 'Yosmanyga\Test\Resource\Util\Fixtures\Foo'
                    )
                ),
                2 => array(
                    'method' => 'bar',
                    'key' => 'AnnotationX',
                    'value' => array(
                        'foo21' => 'bar21',
                        'foo22' => 'bar22'
                    ),
                    'metadata' => array(
                        'class' => 'Yosmanyga\Test\Resource\Util\Fixtures\Foo'
                    )
                ),
                3 => array(
                    'method' => 'bar',
                    'key' => 'AnnotationZ',
                    'value' => array(
                        'foo1' => 'bar1',
                        'foo2' => 'bar2'
                    ),
                    'metadata' => array(
                        'class' => 'Yosmanyga\Test\Resource\Util\Fixtures\Foo'
                    )
                )
            ),
            $docParser->parse(sprintf("%s/Fixtures/Foo.php", dirname(__FILE__)))
        );
    }

    public function testResolveAnnotations()
    {
        $docParser = new DocParser();
        $m = new \ReflectionMethod('Yosmanyga\Resource\Util\DocParser', 'resolveAnnotations');
        $m->setAccessible(true);

        $data = <<<EOF
/**
 * @AnnotationX({
 *  foo1: 'bar1',
 *  foo2: 'bar2'
 * })
 * @AnnotationY({foo1: 'bar1', foo2: 'bar2'})
 */
EOF;
        $this->assertEquals(
            array(
                0 => array(
                    'key' => 'AnnotationX',
                    'value' => "{ foo1: 'bar1', foo2: 'bar2'}"
                ),
                1 => array(
                    'key' => 'AnnotationY',
                    'value' => "{foo1: 'bar1', foo2: 'bar2'}"
                )
            ),
            $m->invoke($docParser, $data)
        );

        $docParser = new DocParser();
        $m = new \ReflectionMethod('Yosmanyga\Resource\Util\DocParser', 'resolveAnnotations');
        $m->setAccessible(true);

        $data = "";
        $this->assertEquals(
            array(),
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
            array(
                0 => "@AnnotationX({foo1: 'bar1', foo2: 'bar2'})",
                1 => "@AnnotationY({foo1: 'bar1', foo2: 'bar2'})"
            ),
            $m->invoke($docParser, $data)
        );

        $data = "@AnnotationX({foo1: 'bar1', foo2: 'bar2'})";
        $this->assertEquals(
            array(
                0 => "@AnnotationX({foo1: 'bar1', foo2: 'bar2'})"
            ),
            $m->invoke($docParser, $data)
        );
    }

    public function testCleanContents()
    {
        $docParser = new DocParser();
        $m = new \ReflectionMethod('Yosmanyga\Resource\Util\DocParser', 'cleanContents');
        $m->setAccessible(true);

        $data = array(
            "@AnnotationX({\nfoo1: 'bar1',\nfoo2: 'bar2'\n})"
        );
        $this->assertEquals(
            array(
                0 => "@AnnotationX({foo1: 'bar1',foo2: 'bar2'})",
            ),
            $m->invoke($docParser, $data)
        );
    }

    public function testParseAnnotations()
    {
        $docParser = new DocParser();
        $m = new \ReflectionMethod('Yosmanyga\Resource\Util\DocParser', 'parseAnnotations');
        $m->setAccessible(true);

        $data = array(
            "@AnnotationX({foo1: 'bar1',foo2: 'bar2'})"
        );
        $this->assertEquals(
            array(
                0 => array(
                    'key' => 'AnnotationX',
                    'value' => "{foo1: 'bar1',foo2: 'bar2'}"
                )
            ),
            $m->invoke($docParser, $data)
        );

        $data = array(
            "@AnnotationX"
        );
        $this->assertEquals(
            array(
                0 => array(
                    'key' => 'AnnotationX',
                    'value' => ""
                )
            ),
            $m->invoke($docParser, $data)
        );
    }
}
