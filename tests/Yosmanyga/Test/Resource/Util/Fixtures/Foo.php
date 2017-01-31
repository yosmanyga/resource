<?php

namespace Yosmanyga\Test\Resource\Util\Fixtures;

/**
 * @AnnotationX({foo11: 'bar11', foo12: 'bar12'})
 */
class Foo
{
    /**
     * @AnnotationY({foo1: 'bar1', foo2: 'bar2'})
     * @AnnotationX()
     */
    private $p;

    /**
     * @AnnotationX({
     *  foo21: 'bar21',
     *  foo22: 'bar22'
     * })
     * @AnnotationZ\Foo({foo1: 'bar1', foo2: 'bar2'})
     */
    public function bar()
    {
    }
}
