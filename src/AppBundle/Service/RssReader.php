<?php
declare(strict_types=1);

namespace AppBundle\Service;


use SimplePie;

class RssReader
{
    public function read(string $url): iterable
    {
        //'https://www.lemonde.fr/rss/une.xml'
        $feed = new SimplePie();
        $feed->set_cache_location(sys_get_temp_dir());
        $feed->set_feed_url($url);

        $feed->init();
        $feed->handle_content_type();

        foreach ($feed->get_items() as $item) {
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