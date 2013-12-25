Resource
========

This library provides tools to define and load resources.

A ```Resource``` is an object containing metadata that refers to something like
 a file, a directory, a db table or anything else with data:

    $resource = new Resource(array(
        'file' => '/path/to/a/file'
    ));

## Reader

You can read a resource by using a ``Reader``. The read is done usually
iterating over the data at first level:

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

## Workflow

This library proposes a workflow for loading a resource:

1. Use a ```Reader``` to iterate over the resource data.
2. Inside each iteration, use a ```Normalizer``` to normalize the data to a
standard format.
3. After getting all data, use a ```Cacher``` to cache the data for future
loading.

# Documentation

Read the documentation for more information.