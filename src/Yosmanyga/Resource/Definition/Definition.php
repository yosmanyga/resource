<?php

namespace Yosmanyga\Resource\Definition;

use Yosmanyga\Validation\Validator\ArrayValidator;
use Yosmanyga\Validation\Validator\ExceptionValidator;

abstract class Definition implements DefinitionInterface
{
    public function import($data)
    {
        $this->validate($data);

        if ($data) {
            foreach ($data as $key => $value) {
                $this->$key = $value;
            }
        }
    }

    private function validate($data)
    {
        $r = new \ReflectionClass($this);
        $properties = $r->getProperties();
        $optionalKeys = [];
        foreach ($properties as $property) {
            $optionalKeys[] = $property->name;
        }

        $validator = new ExceptionValidator(new ArrayValidator([
            'optionalKeys' => $optionalKeys,
            'allowExtra'   => false,
        ]));

        return $validator->validate($data);
    }

    public function export()
    {
        $r = new \ReflectionClass($this);
        $properties = $r->getProperties();
        $export = [];
        foreach ($properties as $property) {
            if (null !== $property->getValue($this)) {
                $export[$property->name] = $property->getValue($this);
            }
        }

        return $export;
    }
}
