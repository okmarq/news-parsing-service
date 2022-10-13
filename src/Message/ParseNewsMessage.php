<?php

namespace App\Message;

final class ParseNewsMessage
{
    /*
     * Add whatever properties and methods you need
     * to hold the data for this message class.
     */

    private $news;

    public function __construct(array $news)
    {
        $this->news = $news;
    }

    public function getNews(): array
    {
        return $this->news;
    }
}
