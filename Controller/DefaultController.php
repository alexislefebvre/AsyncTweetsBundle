<?php

namespace AlexisLefebvre\Bundle\AsyncTweetsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;

class DefaultController extends Controller
{
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
        
        if (count($tweets) > 0)
        {
            $firstTweetId = $tweets[0]->getId();
            
            $previousTweetId = $tweetRepository
                ->getPreviousTweetId($firstTweetId);
            $nextTweetId = $tweetRepository
                ->getNextTweetId($firstTweetId);
            
            if ($request->cookies->has('lastTweetId'))
            {
                $cookieTweetId = $request->cookies->get('lastTweetId');
            }
            
            # Only update the cookie if the last Tweet Id is bigger than
            #  the one in the cookie
            if ($firstTweetId > $cookieTweetId)
            {
                $nextYear = new \Datetime('now');
                $nextYear->add(new \DateInterval('P1Y'));
                
                # Set last Tweet Id
                $cookie = new Cookie('lastTweetId', $firstTweetId,
                    $nextYear->format('U'));
                
                $cookieTweetId = $firstTweetId;
            }
            
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
        
        if (! is_null($cookie))
        {
            $response->headers->setCookie($cookie);
        }
        
        return $response;
    }
    
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
}

