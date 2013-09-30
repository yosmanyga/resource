# Normalizer

Each ```Reader``` returns the data as an array but with it respective format.
That's why you should use a ```Normalizer``` to normalize the data after read
it:

    $reader = new YamlFileReader();
    $normalizer = new MyDataYamlFileNormalizer();

    $reader->open($resource);
    while ($item = $reader->current()) {
        $item = $normalizer->normalize($item, $resource);
        print_r($item);
        $reader->next();
    }
    $reader->close();

In the same way you use a ```DelegatorReader``` you can use
a ```DelegatorNormalizer```:

    $normalizer = new DelegatorNormalizer(array(
        new MyDataYamlFileNormalizer(),
        new MyDataXmlFileNormalizer(),
        new MyDataIniFileNormalizer(),
        new MyDataAnnotationFileNormalizer()
    ));

Keep in mind that you can use the same readers in different places,
but you should implement custom normalizers for your data. Your normalizers
should return data in the same format, maybe an array, or an object, that's your
choice.
