<?php
declare(strict_types=1);

namespace AppBundle\Service;


use SimplePie;

class RssReader
{
    public function read():iterable {
        $feed = new SimplePie();
        $feed->set_feed_url('https://www.lemonde.fr/rss/une.xml');

        $feed->init();


    }
}