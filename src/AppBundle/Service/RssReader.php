<?php
declare(strict_types=1);

namespace AppBundle\Service;


use SimplePie;

class RssReader
{
    /**
     * @var SimplePie
     */
    private $reader;

    public function __construct(SimplePie $reader)
    {
        $this->reader = $reader;
    }

    public function read(string $url): iterable
    {
        $this->reader->set_cache_location(sys_get_temp_dir());
        $this->reader->set_feed_url($url);

        $this->reader->init();
        $this->reader->handle_content_type();

        foreach ($this->reader->get_items() as $item) {
            $path = null;
            if ($item->get_enclosure() !== null) {
                $url = $item->get_enclosure()->get_link();
                $path = tempnam(sys_get_temp_dir(), 'ezdemonews');

                file_put_contents($path, file_get_contents($url));
            }
            $item->image_path = $path;
            yield $item;
        }

    }
}