<?php

namespace AlexisLefebvre\Bundle\AsyncTweetsBundle\Controller;

use AlexisLefebvre\Bundle\AsyncTweetsBundle\Entity\Tweet;
use AlexisLefebvre\Bundle\AsyncTweetsBundle\Entity\TweetRepository;
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
    /** @var TweetRepository */
    private $tweetRepository;

    public function indexAction(Request $request, ?string $firstTweetId): Response
    {
        /** @var TweetRepository $tweetRepository */
        $tweetRepository = $this->getDoctrine()
            ->getRepository('AsyncTweetsBundle:Tweet');

        $this->tweetRepository = $tweetRepository;

        $tweets = $this->tweetRepository
            ->getWithUsersAndMedias($firstTweetId);

        $variables = $this->getVariables($request, $tweets, $firstTweetId);

        $response = $this->render(
            'AsyncTweetsBundle:Default:index.html.twig',
            [
                'tweets' => $tweets,
                'vars'   => $variables,
            ]
        );

        if (!is_null($variables['cookie'])) {
            $response->headers->setCookie($variables['cookie']);
        }

        return $response;
    }

    /**
     * @param Request $request
     * @param Tweet[] $tweets
     * @param string  $firstTweetId
     *
     * @return array $vars
     */
    private function getVariables(Request $request, $tweets, ?string $firstTweetId): array
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
     * @param Tweet[] $tweets
     * @param array   $vars
     */
    private function getTweetsVars($tweets, array $vars): array
    {
        /** @var string $firstTweetId */
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

        $vars['number'] = $this->tweetRepository
            ->countPendingTweets($vars['cookieId']);

        $vars['first'] = $firstTweetId;

        return $vars;
    }

    private function getLastTweetIdFromCookie(Request $request)
    {
        if ($request->cookies->has('lastTweetId')) {
            return $request->cookies->get('lastTweetId');
        }
        // else
    }

    private function createCookie(string $firstTweetId): Cookie
    {
        $nextYear = new \DateTime('now');
        $nextYear->add(new \DateInterval('P1Y'));

        // Set last Tweet Id
        $cookie = new Cookie(
            'lastTweetId',
            $firstTweetId,
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
            $tweetRepository = $this->getDoctrine()
                ->getRepository('AsyncTweetsBundle:Tweet');

            $count = $tweetRepository
                ->deleteAndHideTweetsLessThanId($lastTweetId);

            /** @var Session $session */
            $session = $this->get('session');

            $session->getFlashBag()->add(
                'message',
                sprintf('%s tweets deleted.', $count)
            );
        }

        return $this->redirect($this->generateUrl('asynctweets_homepage'));
    }
}
