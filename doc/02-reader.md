You can read a resource by using a ``Reader``. The read is done usually
iterating over the data:

resource.yml:

    item1:
        item11: value11
        item12: value12
    item2:
        item22: value22

reader.php:

    $reader = new YamlFileReader();
    $reader->open($resource);
    while ($item = $reader->current()) {
        print_r($item);
        $reader->next();
    }
    $reader->close();

output:

    Array
    (
        [key] => item1
        [value] => Array
            (
                [item11] => value11
                [item12] => value12
            )
    )
    Array
    (
        [key] => item2
        [value] => Array
            (
                [item22] => value22
            )

    )

# Delegator Reader

You can create a ``Delegator Reader`` to delegate the read to the right
``Reader``:

    $resource = new Resource(array('file' => '/path/to/resource.ini'));
    $reader = new DelegatorReader(array(
        new YamlFileReader(),
        new XmlFileReader(),
        new IniFileReader(),
        new AnnotationFileReader()
    ));

Internally will be used the right reader, according to the resource type.

# Directory Reader

You can create a ``Directory Reader`` to read files inside a directory:

    $resource = new Resource(array(
        'dir' => '/path/to/dir/'
        'filter' => '*.php',
        'depth' => '>= 0'
        'type' => 'annotation'
    ));

    $reader = new DirectoryReader(array(
        new YamlFileReader(),
        new XmlFileReader(),
        new IniFileReader(),
        new AnnotationFileReader()
    ));

This reader has a restriction, as it can only read resources of same type.
