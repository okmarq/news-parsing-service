<?php

namespace App\Command;

use App\Service\NewsService;
use App\Repository\NewsRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class ParseNewsCommand extends Command
{
    private $NewsRepository;
    private $client;

    protected static $defaultName = 'app:parse-news';
    protected static $defaultDescription = 'This command will start fetching of articles from a news website';

    public function __construct(NewsRepository $NewsRepository, HttpClientInterface $client)
    {
        $this->NewsRepository = $NewsRepository;
        $this->client = $client;

        parent::__construct();
    }

    protected function configure(): void
    {
        // $this
        //     ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
        //     ->addOption('start', null, InputOption::VALUE_NONE, 'Start the parsing')
        // ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        // $arg1 = $input->getArgument('arg1');

        // if ($arg1) {
        //     $io->note(sprintf('You passed an argument: %s', $arg1));
        // }

        // if ($input->getOption('start')) {
        // }
            $io->note('Starting article parsing');

            // parse the articles from he service and save to database from here
            // run every 5 minutes [5 * * * *]
            $parse = new NewsService($this->NewsRepository);
            $response = $parse->fetchNews($this->client);

        $io->success('parsed news articles successfully');
        // $io->success(sprintf('parsed "%d" articles from "%s"', $response['count'], $response['url']));

        return Command::SUCCESS;
    }
}
