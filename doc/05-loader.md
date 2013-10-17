# Loader

You can implement all the workflow inside a ```Loader```:

    class Loader implements LoaderInterface
    {
        /**
         * @var \Yosmanyga\Resource\Reader\Iterator\ReaderInterface
         */
        private $reader;

        /**
         * @var \Yosmanyga\Resource\Normalizer\NormalizerInterface
         */
        private $normalizer;

        /**
         * @var \Yosmanyga\Resource\Cacher\CacherInterface
         */
        private $cacher;

        public function __construct(
            ReaderInterface $reader,
            NormalizerInterface $normalizer,
            CacherInterface $cacher)
        {
            $this->reader = $reader;
            $this->normalizer = $normalizer;
            $this->cacher = $cacher;
        }

        /**
         * @param $resource \Yosmanyga\Resource\Resource
         * @return array
         */
        public function load($resource)
        {
            if ($this->cacher->check($resource)) {
                return $this->cacher->retrieve($resource);
            }

            $this->reader->open($resource);
            $items = array();
            while ($item = $this->reader->current()) {
                $items[] = $this->normalizer->normalize($item, $resource);
                $this->reader->next();
            }
            $reader->close();

            $this->cacher->store($items, $resource);

            return $items;
        }
    }
