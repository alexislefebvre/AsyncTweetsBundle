<?php

namespace AlexisLefebvre\Bundle\AsyncTweetsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;

class DefaultController extends Controller
{
    /**
     * @param Request $request
     * @param string|null $firstTweetId
     * 
     * @return \Symfony\Component\HttpFoundation\Response $response $response
     */
    public function indexAction(Request $request, $firstTweetId = null)
    {
        $previousTweetId = $nextTweetId = null;
        
        # No cookie by default
        $cookie = $cookieTweetId = null;
        
        $tweetRepository = $this->getDoctrine()
            ->getRepository('AsyncTweetsBundle:Tweet');
        
        $tweets = $tweetRepository
            ->getWithUsersAndMedias($firstTweetId);
        
        $numberOfTweets = 0;
        
        if (count($tweets) > 0) {
            $firstTweetId = $tweets[0]->getId();
            
            list($previousTweetId, $nextTweetId) = $tweetRepository
                ->getPreviousAndNextTweetId($firstTweetId);
            
            list($cookie, $cookieTweetId) = $this->getCookieValues($request,
                $firstTweetId);
            
            $numberOfTweets = $tweetRepository
                ->countPendingTweets($cookieTweetId);
        }
        
        $response = $this->render(
            'AsyncTweetsBundle:Default:index.html.twig',
            array(
                'tweets' => $tweets,
                'previousTweetId' => $previousTweetId,
                'nextTweetId' => $nextTweetId,
                'firstTweetId' => $firstTweetId,
                'cookieTweetId' => $cookieTweetId,
                'numberOfTweets' => $numberOfTweets,
            )
        );
        
        if (! is_null($cookie)) {
            $response->headers->setCookie($cookie);
        }
        
        return $response;
    }
    
    /**
     * @param Request $request
     */
    private function getLastTweetIdFromCookie(Request $request)
    {
        if ($request->cookies->has('lastTweetId')) {
            return($request->cookies->get('lastTweetId'));
        }
        // else
        return(null);
    }
    
    /**
     * @param string $firstTweetId
     */
    private function getCookie($firstTweetId)
    {
        $nextYear = new \Datetime('now');
        $nextYear->add(new \DateInterval('P1Y'));
        
        # Set last Tweet Id
        $cookie = new Cookie('lastTweetId', $firstTweetId,
            $nextYear->format('U'));
        
        return($cookie);
    }
    
    /**
     * @param Request $request
     * @param integer $firstTweetId
     */
    private function getCookieValues(Request $request, $firstTweetId)
    {
        $cookie = null;
        $cookieTweetId = $this->getLastTweetIdFromCookie($request);
        
        # Only update the cookie if the last Tweet Id is bigger than
        #  the one in the cookie
        if ($firstTweetId > $cookieTweetId) {
            $cookie = $this->getCookie($firstTweetId);
            
            $cookieTweetId = $firstTweetId;
        }
        
        return array($cookie, $cookieTweetId);
    }
    
    /**
     * @return RedirectResponse $response
     */
    public function resetCookieAction()
    {
        /** @see http://www.craftitonline.com/2011/07/symfony2-how-to-set-a-cookie/ */
        $response = new RedirectResponse(
            $this->generateUrl('asynctweets_homepage')
        );
        
        # Reset last Tweet Id
        $cookie = new Cookie('lastTweetId', null);
        $response->headers->setCookie($cookie);
        
        return $response;
    }
    
    /**
     * @return RedirectResponse $response
     */
    public function deleteLessThanAction(Request $request)
    {
        $lastTweetId = $this->getLastTweetIdFromCookie($request);
        
        if ($lastTweetId) {
            $count = $this->getDoctrine()
                ->getRepository('AsyncTweetsBundle:Tweet')
                ->deleteTweetsLessThanId($lastTweetId);
            
            $this->get('session')->getFlashBag()->add('message',
                sprintf('%s tweets deleted.', $count)
            );
        }
        
        return $this->redirect($this->generateUrl('asynctweets_homepage'));
    }
}

