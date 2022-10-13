<?php

namespace App\Service;

use App\Entity\News;
use App\Repository\NewsRepository;


use Symfony\Component\DomCrawler\Crawler;
use Symfony\Contracts\HttpClient\HttpClientInterface;

use Monolog\DateTimeImmutable;

class NewsService
{
    private const SECONDS_BEFORE_PUBLISHING_JOB = 10;

    private $newsRepository;
    private string $url = 'https://www.nytimes.com/section/technology';
    private int $count = 0;

    public function __construct(NewsRepository $newsRepository)
    {
        $this->newsRepository = $newsRepository;
    }

    /**
     * Fetch and parse news feed
     * @return array
     */
    public function fetchNews(HttpClientInterface $client)
    {
        // paste multiple fetch processes into rabbit mq
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

            // run the cron in this location
            // cron should move it to database
            return $this->save($data);
    }

    /**
     * Save news article if it doesn't already exist in database
     * @param array $data
     * @return void
     */
    private function save(
        array $data
    ): array {
        // post each data into a cron for parsing to a database
        foreach ($data as $value) {
            if ($news = $this->newsRepository->findOneByTitle($value['title'])) {
                $news->setdate_added(new \DateTimeImmutable($value['date_added']));
                $this->newsRepository->add($news, true);
                // if still time, don't update, instead log the date added and show to viewer
                return [];
            } else {
                $news = new News();
                $news->setTitle($value['title']);
                $news->setDescription($value['description']);
                $news->setPicture($value['picture']);
                $news->setdate_added(new \DateTimeImmutable($value['date_added']));
                $this->newsRepository->add($news, true);
            }
            ++$this->count;
        }

        return [
            'count'=>$this->count,
            'website'=>$this->url,
        ];
    }
}
