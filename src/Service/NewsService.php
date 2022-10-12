<?php

namespace App\Service;

use App\Entity\News;
use App\Repository\NewsRepository;


use Symfony\Component\DomCrawler\Crawler;
use Symfony\Contracts\HttpClient\HttpClientInterface;

use Monolog\DateTimeImmutable;

class NewsService
{
    private $newsRepository;
    private $url = 'https://www.nytimes.com/section/technology';

    public function __construct(NewsRepository $newsRepository)
    {
        $this->newsRepository = $newsRepository;
    }

    /**
     * Fetch and parse news feed
     * @return void
     */
    public function fetchNews(HttpClientInterface $client)
    {
        $response = $client->request('GET', $this->url);
        $content = $response->getContent();

        if (empty($content)) {
            return;
        }

        $crawler = new Crawler($content);

        $data = $crawler
            ->filter('ol > li > div > div > a')
            ->each(function ($node) {
                $data['title'] = $node->filter('h2')->text('no title', false);
                $data['description'] = $node
                    ->filter('p')
                    ->text('no description', false);
                $data['picture'] =
                    $node
                        ->filter('div > figure > div > img')
                        ->image()
                        ->getUri() ?? 'no image';
                $data['date_added'] =
                    substr($node->attr('href'), 1, 10) ?? date('Y/m/d');
                return $data;
            });

            // run the rabbit mq in this location
            // rabbit mq should move it to database
            $this->save($data);
    }

    /**
     * Save news article if it doesn't already exist in database
     * @param array $data
     * @return void
     */
    private function save(
        array $data
    ): void {
        foreach ($data as $value) {
            if ($news = $this->newsRepository->findOneByTitle($value['title'])) {
                $news->setDateAdded(new \DateTimeImmutable($value['date_added']));
                $this->newsRepository->add($news, true);
                // if still time, don't update, instead log the date added and show to viewer
            } else {
                $news = new News();
                $news->setTitle($value['title']);
                $news->setDescription($value['description']);
                $news->setPicture($value['picture']);
                $news->setDateAdded(new \DateTimeImmutable($value['date_added']));
                $this->newsRepository->add($news, true);
            }
        }

    }
}
