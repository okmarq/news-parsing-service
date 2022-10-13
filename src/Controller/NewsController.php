<?php

namespace App\Controller;

use App\Entity\News;
use App\Form\NewsType;
use App\Repository\NewsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/news")
 */
class NewsController extends AbstractController
{
    /**
     * @Route("/", name="app_news_index", methods={"GET"})
     */
    public function index(
        Request $request,
        NewsRepository $newsRepository,
        SessionInterface $session
    ): Response {
        if (!in_array($session->get('role'), ['Admin', 'Moderator']))
            return $this->redirectToRoute('app_auth');

        $offset = max(0, $request->query->getInt('offset', 0));
        $perpage = 10;

        $news = $newsRepository->getPaginated($perpage, $offset);

        return $this->render('news/index.html.twig', [
            'news' => $news,
            'pages' => $perpage % count($news),
            'previous' => $offset - $perpage,
            'goto' => $perpage,
            'next' => min(count($news), $offset + $perpage),
        ]);
    }

    /**
     * @Route("/new", name="app_news_new", methods={"GET", "POST"})
     */
    public function new(
        Request $request,
        NewsRepository $newsRepository
    ): Response {
        $news = new News();
        $form = $this->createForm(NewsType::class, $news);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newsRepository->add($news, true);

            return $this->redirectToRoute(
                'app_news_index',
                [],
                Response::HTTP_SEE_OTHER
            );
        }

        return $this->renderForm('news/new.html.twig', [
            'news' => $news,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_news_show", methods={"GET"})
     */
    public function show(News $news): Response
    {
        return $this->render('news/show.html.twig', [
            'news' => $news,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_news_edit", methods={"GET", "POST"})
     */
    public function edit(
        Request $request,
        News $news,
        NewsRepository $newsRepository
    ): Response {
        $form = $this->createForm(NewsType::class, $news);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newsRepository->add($news, true);

            return $this->redirectToRoute(
                'app_news_index',
                [],
                Response::HTTP_SEE_OTHER
            );
        }

        return $this->renderForm('news/edit.html.twig', [
            'news' => $news,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_news_delete", methods={"POST"})
     */
    public function delete(
        Request $request,
        News $news,
        NewsRepository $newsRepository,
        SessionInterface $session
    ): Response {
        if ($session->get('role') !== 'Admin') {
            return $this->redirectToRoute('app_auth');
        }

        if (
            $this->isCsrfTokenValid(
                'delete' . $news->getId(),
                $request->request->get('_token')
            )
        ) {
            $newsRepository->remove($news, true);
        }

        return $this->redirectToRoute(
            'app_news_index',
            [],
            Response::HTTP_SEE_OTHER
        );
    }
}
