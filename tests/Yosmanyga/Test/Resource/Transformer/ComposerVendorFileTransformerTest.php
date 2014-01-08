<?php

namespace Yosmanyga\Test\Resource\Transformer;

use Yosmanyga\Resource\Transformer\ComposerVendorFileTransformer;
use Yosmanyga\Resource\Resource;

class ComposerVendorFileTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Yosmanyga\Resource\Transformer\ComposerVendorFileTransformer::__construct
     */
    public function testConstruct()
    {
        $transformer = new ComposerVendorFileTransformer();
        $class = new \ReflectionClass($transformer);
        $this->assertAttributeEquals(
            sprintf("%s/../../../../../../../vendor/composer/installed.json", dirname($class->getFileName())),
            'file',
            $transformer
        );
        $this->assertAttributeInstanceOf(
            'Composer\Repository\FilesystemRepository',
            'repository',
            $transformer
        );

        $transformer = new ComposerVendorFileTransformer('foo');
        $this->assertAttributeEquals(
            'foo',
            'file',
            $transformer
        );
    }

    /**
     * @covers Yosmanyga\Resource\Transformer\ComposerVendorFileTransformer::supports
     */
    public function testSupports()
    {
        $transformer = new ComposerVendorFileTransformer();

        $this->assertTrue($transformer->supports(
            new Resource(array('file' => '@foo')),
            new Resource()
        ));
        $this->assertFalse($transformer->supports(
            new Resource(),
            new Resource()
        ));
        $this->assertFalse($transformer->supports(
            new Resource(array('file' => 'foo')),
            new Resource()
        ));
    }

    /**
     * @covers Yosmanyga\Resource\Transformer\ComposerVendorFileTransformer::transform
     */
    public function testTransform()
    {
        $transformer = new ComposerVendorFileTransformer();
        $class = new \ReflectionClass($transformer);
        $package1 = $this
            ->getMockBuilder('\Composer\Package\CompletePackage')
            ->disableOriginalConstructor()
            ->getMock();
        $package1
            ->expects($this->once())->method('getName')
            ->will($this->returnValue('owner1/package1'));
        $package1
            ->expects($this->once())->method('getAutoload')
            ->will($this->returnValue(array('psr-0' => array('src/'))));
        $repository = $this
            ->getMockBuilder('\Composer\Repository\FilesystemRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $repository
            ->expects($this->once())->method('getPackages')
            ->will($this->returnValue(array($package1)));
        $property = new \ReflectionProperty($transformer, 'repository');
        $property->setAccessible(true);
        $property->setValue($transformer, $repository);

        $this->assertEquals(
            new Resource(array('file' => sprintf("%s/../../../../../../../vendor/owner1/package1/src/a/path", dirname($class->getFileName())))),
            $transformer->transform(
                new Resource(array('file' => '@owner1/package1:a/path')),
                new Resource()
            )
        );
    }

    /**
     * @covers Yosmanyga\Resource\Transformer\ComposerVendorFileTransformer::parseFile
     */
    public function testParseFile()
    {
        $transformer = new ComposerVendorFileTransformer();
        $method = new \ReflectionMethod($transformer, 'parseFile');
        $method->setAccessible(true);
        $this->assertEquals(array('foo/bar', '/a/path'), $method->invoke($transformer, '@foo/bar:/a/path'));
    }

    /**
     * @covers Yosmanyga\Resource\Transformer\ComposerVendorFileTransformer::parseFile
     * @expectedException \InvalidArgumentException
     */
    public function testParseFileThrowExceptionOnInvalidData()
    {
        $transformer = new ComposerVendorFileTransformer();
        $method = new \ReflectionMethod($transformer, 'parseFile');
        $method->setAccessible(true);
        $method->invoke($transformer, 'foo');
    }

    /**
     * @covers Yosmanyga\Resource\Transformer\ComposerVendorFileTransformer::resolveSrc
     */
    public function testResolveSrc()
    {
        $transformer = new ComposerVendorFileTransformer();
        $package1 = $this
            ->getMockBuilder('\Composer\Package\CompletePackage')
            ->disableOriginalConstructor()
            ->getMock();
        $package1
            ->expects($this->once())->method('getName')
            ->will($this->returnValue('owner1/package1'));
        $package1
            ->expects($this->once())->method('getAutoload')
            ->will($this->returnValue(array('psr-0' => array('src/'))));
        $repository = $this
            ->getMockBuilder('\Composer\Repository\FilesystemRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $repository
            ->expects($this->once())->method('getPackages')
            ->will($this->returnValue(array($package1)));
        $property = new \ReflectionProperty($transformer, 'repository');
        $property->setAccessible(true);
        $property->setValue($transformer, $repository);
        $method = new \ReflectionMethod($transformer, 'resolveSrc');
        $method->setAccessible(true);
        $this->assertEquals(
            'src/',
            $method->invoke($transformer, 'owner1/package1')
        );
    }

    /**
     * @covers Yosmanyga\Resource\Transformer\ComposerVendorFileTransformer::resolveSrc
     * @expectedException \InvalidArgumentException
     */
    public function testResolveSrcThrowExceptionIfVendorNotFound()
    {
        $transformer = new ComposerVendorFileTransformer();
        $package1 = $this
            ->getMockBuilder('\Composer\Package\CompletePackage')
            ->disableOriginalConstructor()
            ->getMock();
        $package1
            ->expects($this->once())->method('getName')
            ->will($this->returnValue('owner1/package1'));
        $repository = $this
            ->getMockBuilder('\Composer\Repository\FilesystemRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $repository
            ->expects($this->once())->method('getPackages')
            ->will($this->returnValue(array($package1)));
        $property = new \ReflectionProperty($transformer, 'repository');
        $property->setAccessible(true);
        $property->setValue($transformer, $repository);
        $method = new \ReflectionMethod($transformer, 'resolveSrc');
        $method->setAccessible(true);
        $method->invoke($transformer, 'owner2/package2');
    }
}
