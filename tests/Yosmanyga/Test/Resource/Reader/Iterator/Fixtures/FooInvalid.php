<?php

namespace Yosmanyga\Test\Resource\Reader\Iterator\Fixtures;

/**
 * @AnnotationX({foo11: 'bar11', foo12: 'bar12'})
 */
class FooInvalids
{
    /**
     * @AnnotationY({foo1: 'bar1', foo2: 'bar2'})
     */
    private $p;

    /**
     * @AnnotationX({
     *  foo21: 'bar21',
     *  foo22: 'bar22'
     * })
     * @AnnotationZ({foo1: 'bar1', foo2: 'bar2'})
     */
    public function bar()
    {
    }
}
