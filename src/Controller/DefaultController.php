<?php

namespace AlexisLefebvre\Bundle\AsyncTweetsBundle\Controller;

use AlexisLefebvre\Bundle\AsyncTweetsBundle\Entity\Tweet;
use AlexisLefebvre\Bundle\AsyncTweetsBundle\Entity\TweetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as BaseController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

// Compatibility layer for Symfony 3.4
if (!class_exists('Symfony\Bundle\FrameworkBundle\Controller\Controller')) {
    class_alias('Symfony\Bundle\FrameworkBundle\Controller\Controller', 'Symfony\Bundle\FrameworkBundle\Controller\AbstractController');
}

class DefaultController extends BaseController
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var TweetRepository */
    private $tweetRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function indexAction(Request $request, ?int $firstTweetId): Response
    {
        /** @var TweetRepository $tweetRepository */
        $tweetRepository = $this->entityManager
            ->getRepository(Tweet::class);

        $this->tweetRepository = $tweetRepository;

        $tweets = $this->tweetRepository
            ->getWithUsersAndMedias($firstTweetId);

        $variables = $this->getVariables($request, $tweets, $firstTweetId);

        $response = $this->render(
            '@AsyncTweets/Default/index.html.twig',
            [
                'tweets' => $tweets,
                'vars'   => $variables,
            ]
        );

        if (!is_null($variables['cookie'])) {
            /** @var Cookie $cookie */
            $cookie = $variables['cookie'];
            $response->headers->setCookie($cookie);
        }

        return $response;
    }

    /**
     * @param array<Tweet> $tweets
     *
     * @return array<Cookie|int|string|null>
     */
    private function getVariables(Request $request, array $tweets, ?int $firstTweetId): array
    {
        $vars = [
            'first'    => $firstTweetId,
            'previous' => null,
            'next'     => null,
            'number'   => 0,
            'cookieId' => $this->getLastTweetIdFromCookie($request),
            // No cookie by default
            'cookie' => null,
        ];

        if (count($tweets) > 0) {
            $vars = $this->getTweetsVars($tweets, $vars);
        }

        return $vars;
    }

    /**
     * If a Tweet is displayed, fetch data from repository.
     *
     * @param array<Tweet>                  $tweets
     * @param array<Cookie|int|string|null> $vars
     *
     * @return array<Cookie|int|string|null>
     */
    private function getTweetsVars(array $tweets, array $vars): array
    {
        /** @var int $firstTweetId */
        $firstTweetId = $tweets[0]->getId();

        $vars['previous'] = $this->tweetRepository
            ->getPreviousTweetId($firstTweetId);
        $vars['next'] = $this->tweetRepository
            ->getNextTweetId($firstTweetId);

        // Only update the cookie if the last Tweet Id is bigger than
        //  the one in the cookie
        if ($firstTweetId > $vars['cookieId']) {
            $vars['cookie'] = $this->createCookie($firstTweetId);
            $vars['cookieId'] = $firstTweetId;
        }

        /** @var int */
        $cookieId = $vars['cookieId'];
        $vars['number'] = $this->tweetRepository
            ->countPendingTweets($cookieId);

        $vars['first'] = $firstTweetId;

        return $vars;
    }

    private function getLastTweetIdFromCookie(Request $request): ?int
    {
        if ($request->cookies->has('lastTweetId')) {
            return (int) $request->cookies->get('lastTweetId');
        }

        return null;
    }

    private function createCookie(int $firstTweetId): Cookie
    {
        $nextYear = new \DateTime('now');
        $nextYear->add(new \DateInterval('P1Y'));

        // Set last Tweet Id
        $cookie = new Cookie(
            'lastTweetId',
            (string) $firstTweetId,
            $nextYear->format('U')
        );

        return $cookie;
    }

    public function resetCookieAction(): RedirectResponse
    {
        /* @see http://www.craftitonline.com/2011/07/symfony2-how-to-set-a-cookie/ */
        $response = new RedirectResponse(
            $this->generateUrl('asynctweets_homepage')
        );

        // Reset last Tweet Id
        $cookie = new Cookie('lastTweetId', null);
        $response->headers->setCookie($cookie);

        return $response;
    }

    public function deleteLessThanAction(Request $request): RedirectResponse
    {
        $lastTweetId = $this->getLastTweetIdFromCookie($request);

        if (!is_null($lastTweetId)) {
            /** @var TweetRepository $tweetRepository */
            $tweetRepository = $this->entityManager
                ->getRepository(Tweet::class);

            $count = $tweetRepository
                ->deleteAndHideTweetsLessThanId($lastTweetId);

            /** @var Session<int> $session */
            $session = $this->get('session');

            $session->getFlashBag()->add(
                'message',
                sprintf('%s tweets deleted.', $count)
            );
        }

        return $this->redirect($this->generateUrl('asynctweets_homepage'));
    }
}
