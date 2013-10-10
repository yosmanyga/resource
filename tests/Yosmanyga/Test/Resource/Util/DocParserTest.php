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
                    )
                ),
                1 => array(
                    'property' => 'p',
                    'key' => 'AnnotationY',
                    'value' => array(
                        'foo1' => 'bar1',
                        'foo2' => 'bar2'
                    )
                ),
                2 => array(
                    'method' => 'bar',
                    'key' => 'AnnotationX',
                    'value' => array(
                        'foo21' => 'bar21',
                        'foo22' => 'bar22'
                    )
                ),
                3 => array(
                    'method' => 'bar',
                    'key' => 'AnnotationZ',
                    'value' => array(
                        'foo1' => 'bar1',
                        'foo2' => 'bar2'
                    )
                )
            ),
            $docParser->parse(sprintf("%s/Fixtures/Foo.php", dirname(__FILE__)))
        );
    }

    public function testRemoveComment()
    {
        $docParser = new DocParser();
        $m = new \ReflectionMethod('Yosmanyga\Resource\Util\DocParser', 'removeComment');
        $m->setAccessible(true);
        $data = "/**
 * @AnnotationX({
 *  foo21: 'bar21',
 *  foo22: 'bar22'
 * })
 * @AnnotationZ({foo1: 'bar1', foo2: 'bar2'})
 */";
        $this->assertEquals(
            array(
                0 => array(
                    'key' => 'AnnotationX',
                    'value' => "{ foo21: 'bar21', foo22: 'bar22'}"
                ),
                1 => array(
                    'key' => 'AnnotationZ',
                    'value' => "{foo1: 'bar1', foo2: 'bar2'}"
                )
            ),
            $m->invoke($docParser, $data)
        );

        $data = "/**
 *
 */";
        $this->assertEquals(
            array(),
            $m->invoke($docParser, $data)
        );
    }
}
