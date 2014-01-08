<?php

namespace Yosmanyga\Resource\Transformer;

use Yosmanyga\Resource\Resource;
use Composer\Repository\FilesystemRepository;
use Composer\Json\JsonFile;

class ComposerVendorFileTransformer implements TransformerInterface
{
    /**
     * @var string
     */
    private $file;

    /**
     * @var \Composer\Repository\FilesystemRepository
     */
    private $repository;

    /**
     * @param string $file
     */
    public function __construct($file = '')
    {
        $this->file = $file ?: sprintf("%s/../../../../../../../vendor/composer/installed.json", __DIR__);
        $this->repository = new FilesystemRepository(new JsonFile($this->file));
    }

    /**
     * @inheritdoc
     */
    public function supports(Resource $resource, Resource $parentResource)
    {
        if ($resource->hasMetadata('file') && 0 === strpos($resource->getMetadata('file'), '@')) {
            return true;
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function transform(Resource $resource, Resource $parentResource)
    {
        list($vendor, $path) = $this->parseFile($resource->getMetadata('file'));

        $file = sprintf(
            "%s/%s/%s%s",
            dirname(dirname($this->file)),
            $vendor,
            $this->resolveSrc($vendor),
            $path
        );

        return new Resource(array('file' => $file));
    }

    /**
     * @param string $file
     * @throws \InvalidArgumentException if $file has invalid syntax
     * @return array
     */
    private function parseFile($file)
    {
        $data = sscanf($file, '@%[^:]:%[^?]');

        if (empty($data[0])) {
            throw new \InvalidArgumentException(sprintf("File \"%s\" has invalid syntax", $file));
        }

        return $data;
    }

    /**
     * @param string $vendor
     * @throws \InvalidArgumentException if $vendor is not found
     * @return string
     */
    private function resolveSrc($vendor)
    {
        $src = null;

        /** @var $packages \Composer\Package\CompletePackage[] */
        $packages = $this->repository->getPackages();
        foreach ($packages as $package) {
            if ($vendor == $package->getName()) {
                $autoload = $package->getAutoload();
                $src = current($autoload['psr-0']);

                break;
            }
        }

        if (null === $src) {
            throw new \InvalidArgumentException(sprintf("Vendor \"%s\" not found", $vendor));
        }

        return $src;
    }
}
