<?php

namespace AlexisLefebvre\Bundle\AsyncTweetsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    private $tweetRepository;

    /**
     * @param Request     $request
     * @param string|null $firstTweetId
     *
     * @return \Symfony\Component\HttpFoundation\Response $response
     */
    public function indexAction(Request $request, $firstTweetId = null)
    {
        $this->tweetRepository = $this->getDoctrine()
            ->getRepository('AsyncTweetsBundle:Tweet');

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
     * @param Request  $request
     * @param Tweets[] $tweets
     * @param int      $firstTweetId
     *
     * @return array $vars
     */
    private function getVariables(Request $request, $tweets, $firstTweetId)
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
     * @param Tweets[] $tweets
     * @param array    $vars
     *
     * @return array $vars
     */
    private function getTweetsVars($tweets, $vars)
    {
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

    /**
     * @param Request $request
     *
     * @return int|null
     */
    private function getLastTweetIdFromCookie(Request $request)
    {
        if ($request->cookies->has('lastTweetId')) {
            return $request->cookies->get('lastTweetId');
        }
        // else
    }

    /**
     * @param string $firstTweetId
     *
     * @return Cookie $cookie
     */
    private function createCookie($firstTweetId)
    {
        $nextYear = new \Datetime('now');
        $nextYear->add(new \DateInterval('P1Y'));

        // Set last Tweet Id
        $cookie = new Cookie(
            'lastTweetId',
            $firstTweetId,
            $nextYear->format('U')
        );

        return $cookie;
    }

    /**
     * @return RedirectResponse $response
     */
    public function resetCookieAction()
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

    /**
     * @param Request $request
     *
     * @return RedirectResponse $response
     */
    public function deleteLessThanAction(Request $request)
    {
        $lastTweetId = $this->getLastTweetIdFromCookie($request);

        if (!is_null($lastTweetId)) {
            $count = $this->getDoctrine()
                ->getRepository('AsyncTweetsBundle:Tweet')
                ->deleteAndHideTweetsLessThanId($lastTweetId);

            $this->get('session')->getFlashBag()->add(
                'message',
                sprintf('%s tweets deleted.', $count)
            );
        }

        return $this->redirect($this->generateUrl('asynctweets_homepage'));
    }
}
