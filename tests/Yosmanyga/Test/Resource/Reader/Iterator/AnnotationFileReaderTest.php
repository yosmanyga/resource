<?php

namespace Yosmanyga\Test\Resource\Reader\Iterator;

use Yosmanyga\Resource\Util\DocParser;
use Yosmanyga\Resource\Reader\Iterator\AnnotationFileReader;
use Yosmanyga\Resource\Resource;

class AnnotationFileReaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Yosmanyga\Resource\Reader\Iterator\AnnotationFileReader::__construct
     */
    public function testConstructor()
    {
        $reader = new AnnotationFileReader();
        $this->assertAttributeInstanceOf('Yosmanyga\Resource\Util\DocParserInterface', 'docParser', $reader);

        $docParser = new DocParser();
        $reader = new AnnotationFileReader($docParser);
        $this->assertAttributeEquals($docParser, 'docParser', $reader);
    }

    /**
     * @covers Yosmanyga\Resource\Reader\Iterator\AnnotationFileReader::supports
     */
    public function testSupports()
    {
        $reader = new AnnotationFileReader();

        // Right type
        $this->assertTrue($reader->supports(new Resource(array(), 'annotation')));
        // Wrong type
        $this->assertFalse($reader->supports(new Resource(array(), 'foo')));
        // No type
        $this->assertFalse($reader->supports(new Resource(array())));
    }

    /**
     * @covers Yosmanyga\Resource\Reader\Iterator\AnnotationFileReader::open
     */
    public function testOpen()
    {
        $resource = new Resource(array('file' => sprintf("%s/Fixtures/Foo.php", dirname(__FILE__)), 'annotation' => 'AnnotationX'));
        $reader = new AnnotationFileReader();

        $reader->open($resource);
        $this->assertEquals(
            array(
                'key' => 0,
                'value' => array(
                    'class' => 'Yosmanyga\Test\Resource\Reader\Iterator\Fixtures\Foo',
                    'key' => 'AnnotationX',
                    'value' => array(
                        'foo11' => 'bar11',
                        'foo12' => 'bar12'
                    )
                )
            ),
            $reader->current()
        );
    }

    /**
     * @covers Yosmanyga\Resource\Reader\Iterator\AnnotationFileReader::open
     * @expectedException \InvalidArgumentException
     */
    public function testOpenThrowsExceptionWithNotFoundFileMetadata()
    {
        $reader = new AnnotationFileReader();
        $reader->open(new Resource(array('file' => '')));
    }

    /**
     * @covers Yosmanyga\Resource\Reader\Iterator\AnnotationFileReader::open
     * @expectedException \InvalidArgumentException
     */
    public function testOpenThrowsExceptionWithInvalidFileMetadata()
    {
        $reader = new AnnotationFileReader();
        $reader->open(new Resource(array('file' => sprintf("%s/Fixtures/FooInvalid.php", dirname(__FILE__)))));
    }

    /**
     * @covers Yosmanyga\Resource\Reader\Iterator\AnnotationFileReader::current
     * @covers Yosmanyga\Resource\Reader\Iterator\AnnotationFileReader::next
     */
    public function testNext()
    {
        $reader = new AnnotationFileReader();
        $reader->open(new Resource(array('file' => sprintf("%s/Fixtures/Foo.php", dirname(__FILE__)), 'annotation' => 'AnnotationX')));

        $reader->next();
        $this->assertEquals(
            array(
                'key' => 1,
                'value' => array(
                    'method' => 'bar',
                    'key' => 'AnnotationX',
                    'value' => array(
                        'foo21' => 'bar21',
                        'foo22' => 'bar22'
                    )
                )
            ),
            $reader->current()
        );
        $reader->next();
        $this->assertFalse($reader->current());
    }

    /**
     * @covers Yosmanyga\Resource\Reader\Iterator\AnnotationFileReader::current
     * @expectedException \RuntimeException
     */
    public function testCurrentThrowsExceptionWhenNoOpenResource()
    {
        $reader = new AnnotationFileReader();
        $reader->current();
    }

    /**
     * @covers Yosmanyga\Resource\Reader\Iterator\AnnotationFileReader::next
     * @expectedException \RuntimeException
     */
    public function testNextThrowsExceptionWhenNoOpenResource()
    {
        $reader = new AnnotationFileReader();
        $reader->next();
    }

    /**
     * @covers Yosmanyga\Resource\Reader\Iterator\AnnotationFileReader::close
     * @expectedException \RuntimeException
     */
    public function testClose()
    {
        $reader = new AnnotationFileReader();
        $reader->open(new Resource(array('file' => sprintf("%s/Fixtures/Foo.php", dirname(__FILE__)), 'annotation' => 'AnnotationX')));
        $reader->close();
        $reader->current();
    }

    /**
     * @covers Yosmanyga\Resource\Reader\Iterator\AnnotationFileReader::close
     * @expectedException \RuntimeException
     */
    public function testCloseThrowsExceptionWhenNoOpenResource()
    {
        $reader = new AnnotationFileReader();
        $reader->close();
    }

    /**
     * @covers Yosmanyga\Resource\Reader\Iterator\AnnotationFileReader::__clone
     */
    public function testClone()
    {
        $reader = new AnnotationFileReader();

        $r = new \ReflectionClass('Yosmanyga\Resource\Reader\Iterator\AnnotationFileReader');
        $p1 = $r->getProperty('inMemoryReader');
        $p1->setAccessible(true);
        $p1->setValue($reader, new \stdClass());
        $p2 = $r->getProperty('docParser');
        $p2->setAccessible(true);
        $p2->setValue($reader, new \stdClass());
        $clone = clone $reader;
        $this->assertNotSame($p1->getValue($reader), $p1->getValue($clone));
        $this->assertNotSame($p2->getValue($reader), $p2->getValue($clone));
    }
}
