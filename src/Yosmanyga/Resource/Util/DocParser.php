<?php

namespace Yosmanyga\Resource\Util;

use Symfony\Component\Yaml\Yaml;

class DocParser implements DocParserInterface
{
    /**
     * @inheritdoc
     */
    public function parse($file, $annotationName = null)
    {
        $data = array();
        $class = $this->findClass($file);
        $ref = new \ReflectionClass($class);
        $annotations = $this->resolveAnnotations($ref->getDocComment());
        foreach ($annotations as $annotation) {
            if (!$annotationName || preg_match($annotationName, $annotation['key'])) {
                $data[] = array(
                    'class' => $class,
                    'key' => $annotation['key'],
                    'value' => (array) Yaml::parse($annotation['value']),
                    'metadata' => array(
                        'class' => $class
                    )
                );
            }
        }

        foreach ($ref->getProperties() as $property) {
            $annotations = $this->resolveAnnotations($property->getDocComment());
            foreach ($annotations as $annotation) {
                if (!$annotationName || preg_match($annotationName, $annotation['key'])) {
                    $data[] = array(
                        'property' => $property->getName(),
                        'key' => $annotation['key'],
                        'value' => (array) Yaml::parse($annotation['value']),
                        'metadata' => array(
                            'class' => $class
                        )
                    );
                }
            }
        }

        foreach ($ref->getMethods() as $method) {
            $annotations = $this->resolveAnnotations($method->getDocComment());
            foreach ($annotations as $annotation) {
                if (!$annotationName || preg_match($annotationName, $annotation['key'])) {
                    $data[] = array(
                        'method' => $method->getName(),
                        'key' => $annotation['key'],
                        'value' => (array) Yaml::parse($annotation['value']),
                        'metadata' => array(
                            'class' => $class
                        )
                    );
                }
            }
        }

        return $data;
    }

    /**
     * Copied from symfony/symfony/src/Symfony/Component/Routing/Loader/AnnotationFileLoader.php::findClass
     * @codeCoverageIgnore
     */
    private function findClass($file)
    {
        $class = false;
        $namespace = false;
        $tokens = token_get_all(file_get_contents($file));
        for ($i = 0, $count = count($tokens); $i < $count; $i++) {
            $token = $tokens[$i];

            if (!is_array($token)) {
                continue;
            }

            if (true === $class && T_STRING === $token[0]) {
                return $namespace.'\\'.$token[1];
            }

            if (true === $namespace && T_STRING === $token[0]) {
                $namespace = '';
                do {
                    $namespace .= $token[1];
                    $token = $tokens[++$i];
                } while ($i < $count && is_array($token) && in_array($token[0], array(T_NS_SEPARATOR, T_STRING)));
            }

            if (T_CLASS === $token[0]) {
                $class = true;
            }

            if (T_NAMESPACE === $token[0]) {
                $namespace = true;
            }
        }

        return false;
    }

    /**
     * @param string $comment
     * @return array
     */
    private function resolveAnnotations($comment)
    {
        if (!$comment) {
            return array();
        }

        $comment = $this->cleanAnnotations($comment);
        $annotations = $this->splitAnnotations($comment);
        $annotations = $this->cleanContents($annotations);
        $annotations = $this->parseAnnotations($annotations);

        return $annotations;
    }

    /**
     * Copied from phpDocumentor/ReflectionDocBlock/src/phpDocumentor/Reflection/DocBlock.php::cleanInput
     * @codeCoverageIgnore
     */
    private function cleanAnnotations($comment)
    {
        $comment = trim(
            preg_replace(
                '#[ \t]*(?:\/\*\*|\*\/|\*)?[ \t]{0,1}(.*)?#u',
                '$1',
                $comment
            )
        );

        // reg ex above is not able to remove */ from a single line docblock
        if (substr($comment, -2) == '*/') {
            $comment = trim(substr($comment, 0, -2));
        }

        // normalize strings
        $comment = str_replace(array("\r\n", "\r"), "\n", $comment);

        return $comment;
    }

    private function splitAnnotations($comment)
    {
        if (strpos($comment, "\n@")) {
            $comment = "\n" . $comment;
            $comment = str_replace("\n@", "\n@@", $comment);
            $comment = explode("\n@", $comment);
            array_shift($comment);
        } else {
            $comment = array($comment);
        }

        return $comment;
    }

    private function cleanContents($annotations)
    {
        $data = array();
        foreach ($annotations as $annotation) {
            $data[] = str_replace("\n", "", $annotation);
        }

        return $data;
    }

    private function parseAnnotations($annotations)
    {
        $parsedAnnotations = array();
        foreach ($annotations as $annotation) {
            $key = substr(strstr($annotation, '(', true), 1);
            if ($key) {
                $parsedAnnotation['key'] = $key;
                $parsedAnnotation['value'] = substr(strstr($annotation, '('), 1, -1);
                $parsedAnnotations[] = $parsedAnnotation;
            } else {
                $key = substr($annotation, 1);
                if ($key) {
                    $parsedAnnotation['key'] = $key;
                    $parsedAnnotation['value'] = '';
                    $parsedAnnotations[] = $parsedAnnotation;
                }
            }
        }

        return $parsedAnnotations;
    }
}
