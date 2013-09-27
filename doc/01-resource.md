# Resource

A ```Resource``` is a class containing metadata that refers to something like
a file, a directory, a db table or anything else with data:

    $resource = new Resource(
        array('file' => '/path/to/a/file')
    );

## Resource Type

Each resource has a type. It can be deduced internally, or can be declared
explicitly:

    $resource = new Resource(
        array('/file/to/file.php'),
        'annotation'
    );

