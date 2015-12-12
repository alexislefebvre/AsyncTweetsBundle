<?php

namespace AlexisLefebvre\Bundle\AsyncTweetsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;

class DefaultController extends Controller
{
    private $tweetRepository;
    
    /**
     * @param string|null $firstTweetId
     * 
     * @return \Symfony\Component\HttpFoundation\Response $response $response
     */
    public function indexAction($firstTweetId = null)
    {
        $this->tweetRepository = $this->getDoctrine()
            ->getRepository('AsyncTweetsBundle:Tweet');
        
        $tweets = $this->tweetRepository
            ->getWithUsersAndMedias($firstTweetId);
        
        $variables = $this->getVariables($tweets, $firstTweetId);
        
        $response = $this->render(
            'AsyncTweetsBundle:Default:index.html.twig',
            array(
                'tweets' => $tweets,
                'vars' => $variables,
            )
        );
        
        if (! is_null($variables['cookie'])) {
            $response->headers->setCookie($variables['cookie']);
        }
        
        return $response;
    }
    
    /**
     * @param Tweets[] $tweets
     * @param integer $firstTweetId
     * 
     * @return array $vars
     */
    private function getVariables($tweets, $firstTweetId)
    {
        $vars = array(
            'first' => $firstTweetId,
            'previous' => null,
            'next' => null,
            'number' => 0,
            # No cookie by default
            'cookieId' => null,
            'cookie' => null,
        );
        
        if (count($tweets) > 0) {
            $vars = $this->getTweetsVars($tweets, $vars);
        }
        
        return($vars);
    }
    
    /**
     * If a Tweet is displayed, fetch data from repository
     * 
     * @param Tweets[] $tweets
     * @param array $vars
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
        
        # Only update the cookie if the last Tweet Id is bigger than
        #  the one in the cookie
        if ($firstTweetId > $vars['cookieId']) {
            $vars['cookie'] = $this->getCookie($firstTweetId);
            $vars['cookieId'] = $firstTweetId;
        }    
        
        $vars['number'] = $this->tweetRepository
            ->countPendingTweets($vars['cookieId']);
        
        $vars['first'] = $firstTweetId;
        
        return($vars);
    }
    
    /**
     * @param Request $request
     * @return integer|null
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
     * @return Cookie $cookie
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
     * @param Request $request
     * @return RedirectResponse $response
     */
    public function deleteLessThanAction(Request $request)
    {
        $lastTweetId = $this->getLastTweetIdFromCookie($request);
        
        if ($lastTweetId) {
            $count = $this->getDoctrine()
                ->getRepository('AsyncTweetsBundle:Tweet')
                ->deleteAndHideTweetsLessThanId($lastTweetId);
            
            $this->get('session')->getFlashBag()->add('message',
                sprintf('%s tweets deleted.', $count)
            );
        }
        
        return $this->redirect($this->generateUrl('asynctweets_homepage'));
    }
}
