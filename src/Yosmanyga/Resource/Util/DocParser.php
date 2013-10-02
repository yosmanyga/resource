<?php

namespace Yosmanyga\Resource\Util;

use Symfony\Component\Yaml\Yaml;

/**
 * TODO: Improve this class with a descent parser
 */
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
        $annotations = $this->removeComment($ref->getDocComment());
        foreach ($annotations as $annotation) {
            if (!$annotationName || $annotationName == $annotation['key']) {
                $data[] = array(
                    'class' => $class,
                    'key' => $annotation['key'],
                    'value' => Yaml::parse($annotation['value'])
                );
            }
        }

        foreach ($ref->getProperties() as $property) {
            $annotations = $this->removeComment($property->getDocComment());
            foreach ($annotations as $annotation) {
                if (!$annotationName || $annotationName == $annotation['key']) {
                    $data[] = array(
                        'property' => $property->getName(),
                        'key' => $annotation['key'],
                        'value' => Yaml::parse($annotation['value'])
                    );
                }
            }
        }

        foreach ($ref->getMethods() as $method) {
            $annotations = $this->removeComment($method->getDocComment());
            foreach ($annotations as $annotation) {
                if (!$annotationName || $annotationName == $annotation['key']) {
                    $data[] = array(
                        'method' => $method->getName(),
                        'key' => $annotation['key'],
                        'value' => Yaml::parse($annotation['value'])
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
     * @param string $data
     * @return array
     */
    private function removeComment($data)
    {
        // Internal comment
        $data = preg_replace("/\n\s\s+/", "\n", $data);
        $data = str_replace("\n* ", "\n", $data);
        $data = str_replace("\n * ", "\n", $data);
        // Top comment
        $data = str_replace("/**\n", "", $data);
        // Bottom comment
        $data = str_replace("\n*/", "", $data);
        $data = str_replace("\n */", "", $data);

        if (strpos($data, "\n@")) {
            $data = "\n" . $data;
            $data = str_replace("\n@", "\n@@", $data);
            $data = explode("\n@", $data);
            array_shift($data);
        } else {
            $data = array($data);
        }

        array_walk($data, function(&$value) {
            $value = str_replace("\n", "", $value);
        });

        $annotations = array();
        foreach ($data as $d) {
            $key = substr(strstr($d, '(', true), 1);
            if ($key) {
                $annotation['key'] = $key;
                $annotation['value'] = substr(strstr($d, '('), 1, -1);
                $annotations[] = $annotation;
            }
        }

        return $annotations;
    }
}
