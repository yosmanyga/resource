# Cacher

The process of read a resource can be very slow, that's why you should always
use cache.

    $cacher = new Cacher(
        new FileVersionChecker('path/to/cache/', '.check')
        new FileStorer('path/to/cache/')
    );

    if ($cacher->check($resource)) {
        return $cacher->retrieve($resource);
    }

First argument is a ```Checker``` class, used to check if a resource has valid
cache or not.

A common ```Checker``` is the ```FileVersionChecker```, it
determines if the resource has changed since it was cached. So, the cache is
used just if the file is the same.

Another common ```Checker``` is the ```TtlChecker```, it validates the cache by
a number of seconds, after that time the cache is considered invalid.

Second argument is a ```Storer``` class, used to store cache data.

A common ```Storer``` is the ```FileStorer``` class. It uses the filesystem to
store the cache.