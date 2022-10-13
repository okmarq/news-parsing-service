<?php

namespace App\Command;

use App\Message\ParseNewsMessage;
use App\Repository\NewsRepository;
use App\Service\NewsService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class ParseNewsCommand extends Command
{
    protected static $defaultName = 'app:parse-news';
    protected static $defaultDescription = 'This command will start fetching of articles from a news website';
    protected $bus;
    private $client;
    private $newsRepository;

    public function __construct(
        MessageBusInterface $bus,
        HttpClientInterface $client,
        NewsRepository $newsRepository
    ) {
        parent::__construct();
        $this->bus = $bus;
        $this->client = $client;
        $this->newsRepository = $newsRepository;
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {
        $io = new SymfonyStyle($input, $output);

        $io->note('Starting article parsing');

        $newsService = new newsService($this->newsRepository);
        $news = $newsService->fetchNews($this->client);

        $this->bus->dispatch(new ParseNewsMessage($news));

        $io->success('parsed news articles successfully');

        return Command::SUCCESS;
    }
}
