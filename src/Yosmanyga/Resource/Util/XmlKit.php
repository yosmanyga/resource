<?php

namespace Yosmanyga\Resource\Util;

class XmlKit
{
    /**
     * Copied from Symfony\Component\Config\Util\XmlUtils.
     *
     * @author Fabien Potencier <fabien@symfony.com>
     * @author Martin Hasoň <martin.hason@gmail.com>
     *
     * Converts a \DomElement object to a PHP array.
     *
     * The following rules applies during the conversion:
     *
     *  * Each tag is converted to a key value or an array
     *    if there is more than one "value"
     *
     *  * The content of a tag is set under a "value" key (<foo>bar</foo>)
     *    if the tag also has some nested tags
     *
     *  * The attributes are converted to keys (<foo foo="bar"/>)
     *
     *  * The nested-tags are converted to keys (<foo><foo>bar</foo></foo>)
     *
     * @param \DomElement $element     A \DomElement instance
     * @param bool        $checkPrefix Check prefix in an element or an attribute name
     * @codeCoverageIgnore
     *
     * @return array A PHP array
     */
    public function convertDomElementToArray(\DomElement $element, $checkPrefix = true)
    {
        $prefix = (string) $element->prefix;
        $empty = true;
        $config = [];
        foreach ($element->attributes as $name => $node) {
            if ($checkPrefix && !in_array((string) $node->prefix, ['', $prefix], true)) {
                continue;
            }
            $config[$name] = $this->phpize($node->value);
            $empty = false;
        }

        $nodeValue = false;
        foreach ($element->childNodes as $node) {
            if ($node instanceof \DOMText) {
                if (trim($node->nodeValue)) {
                    $nodeValue = trim($node->nodeValue);
                    $empty = false;
                }
            } elseif ($checkPrefix && $prefix != (string) $node->prefix) {
                continue;
            } elseif (!$node instanceof \DOMComment) {
                $value = static::convertDomElementToArray($node, $checkPrefix);

                $key = $node->localName;
                if (isset($config[$key])) {
                    if (!is_array($config[$key]) || !is_int(key($config[$key]))) {
                        $config[$key] = [$config[$key]];
                    }
                    $config[$key][] = $value;
                } else {
                    $config[$key] = $value;
                }

                $empty = false;
            }
        }

        if (false !== $nodeValue) {
            $value = $this->phpize($nodeValue);
            if (count($config)) {
                $config['value'] = $value;
            } else {
                $config = $value;
            }
        }

        return !$empty ? $config : null;
    }

    /**
     * Copied from Symfony\Component\Config\Util\XmlUtils.
     *
     * @author Fabien Potencier <fabien@symfony.com>
     * @author Martin Hasoň <martin.hason@gmail.com>
     *
     * Converts an xml value to a PHP type.
     *
     * @param mixed $value
     * @codeCoverageIgnore
     *
     * @return mixed
     */
    public function phpize($value)
    {
        $value = (string) $value;
        $lowercaseValue = strtolower($value);

        switch (true) {
            case 'null' === $lowercaseValue:
                return;
            case ctype_digit($value):
                $raw = $value;
                $cast = intval($value);

                return '0' == $value[0] ? octdec($value) : (((string) $raw == (string) $cast) ? $cast : $raw);
            case isset($value[1]) && '-' === $value[0] && ctype_digit(substr($value, 1)):
                $raw = $value;
                $cast = intval($value);

                return '0' == $value[1] ? octdec($value) : (((string) $raw == (string) $cast) ? $cast : $raw);
            case 'true' === $lowercaseValue:
                return true;
            case 'false' === $lowercaseValue:
                return false;
            case isset($value[1]) && '0b' == $value[0].$value[1]:
                return bindec($value);
            case is_numeric($value):
                return '0x' == $value[0].$value[1] ? hexdec($value) : floatval($value);
            case preg_match('/^(-|\+)?[0-9]+(\.[0-9]+)?$/', $value):
                return floatval($value);
            default:
                return $value;
        }
    }

    /**
     * @param array $nodes
     *
     * @return array
     */
    public function extractContent($nodes)
    {
        if (isset($nodes['name'])) {
            $nodes = [$nodes];
        }

        $content = [];
        foreach ($nodes as $node) {
            $name = $node['name'];
            unset($node['name']);
            $content[$name] = current($node);
        }

        return $content;
    }
}
