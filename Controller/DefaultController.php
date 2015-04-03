<?php

namespace AlexisLefebvre\Bundle\AsyncTweetsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;

class DefaultController extends Controller
{
    public function indexAction(Request $request, $lastTweetId = null,
        $orderByUser = false)
    {
        $tweets = $this->getDoctrine()
            ->getRepository('AsyncTweetsBundle:Tweet')
            ->getWithUsersAndMedias($lastTweetId, $orderByUser);
        
        $nextLastTweetId = null;
        
        if (count($tweets) > 0)
        {
            $lastTweetId = $tweets[0]->getId();
            
            $nextLastTweetId = $tweets[count($tweets) - 1]->getId();
            
            # Only perform addition on a 64-bits system
            /** @see http://stackoverflow.com/questions/2353473/can-php-tell-if-the-server-os-it-64-bit/6304354#6304354 */
            if (PHP_INT_SIZE === 8)
            {
                $nextLastTweetId = ($tweets[count($tweets) - 1]->getId() + 1);
            }
            
            $numberOfTweets = $this->getDoctrine()
                ->getRepository('AsyncTweetsBundle:Tweet')
                ->countPendingTweets($lastTweetId);
        }
        else
        {
            $lastTweetId = $request->cookies->get('lastTweetId');
            $numberOfTweets = 0;
        }
        
        $lastTweetIdCookie = $lastTweetId;
        
        if ($request->cookies->has('lastTweetId'))
        {
            $lastTweetIdCookie = $request->cookies->get('lastTweetId');
        }
        
        if ($orderByUser)
        {
            $route = 'asynctweets_tweets_orderByUser_sinceTweetId';
            $activeTab = 'tweets_orderByUser';
        }
        else
        {
            $route = 'asynctweets_tweets_sinceTweetId';
            $activeTab = 'tweets';
        }
        
        # No cookie by default
        $cookie = null;
        
        if (
            (! is_null($lastTweetId))
            &&
            # Only update the cookie if the last Tweet Id is bigger than
            #  the one in the cookie
            ($lastTweetId > $request->cookies->get('lastTweetId'))
        )
        {
            $nextYear = new \Datetime('now');
            $nextYear->add(new \DateInterval('P1Y'));
            
            # Set last Tweet Id
            $cookie = new Cookie('lastTweetId', $lastTweetId,
                $nextYear->format('U'));
            
            $lastTweetIdCookie = $lastTweetId;
        }
        
        $response = $this->render(
            'AsyncTweetsBundle:Default:index.html.twig',
            array(
                'route' => $route,
                'activeTab' => $activeTab,
                'tweets' => $tweets,
                'lastTweetId' => $lastTweetId,
                'lastTweetIdCookie' => $lastTweetIdCookie,
                'nextLastTweetId' => $nextLastTweetId,
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

