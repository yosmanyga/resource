# Cacher

The process of read a resource can be very slow, that's why you should always
use cache.

    $cacher = new Cacher(
        new DataFileStorer('path/to/cache/')
        new VersionFileStorer('path/to/cache/', '.meta')
        new FileVersionChecker()
    );

    if ($cacher->check($resource)) {
        return $cacher->retrieve($resource);
    }

First and second arguments are ```Storer``` classes,
used to store the resource data and version. Third argument is used to detect
the resource version. This version determines if the resource has changed
since it was cached. So, the cache is used just if the resource version is the
same.
