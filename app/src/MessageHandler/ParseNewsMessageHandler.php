<?php

namespace App\MessageHandler;

use App\Message\ParseNewsMessage;
use App\Service\NewsService;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class ParseNewsMessageHandler implements MessageHandlerInterface
{
    private $newsService;

    public function __construct(NewsService $newsService)
    {
        $this->newsService = $newsService;
    }

    public function __invoke(ParseNewsMessage $message)
    {
        // do something with your message
        $this->newsService->save($message->getNews());
        sleep(5);
    }
}
