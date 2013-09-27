# Transformer

If you need to import a resource inside another resource then a
```Transformer``` is usefull.

In the following example the imported resource is declared as a relative
resource. Then you should use a ```RelativeFileTransformer``` to transform it
from relative to absolute:

/path/to/resource1.yml

    item1:
        item11: value11
    import:
        file: resource2.yml