<?php

namespace AlexisLefebvre\Bundle\AsyncTweetsBundle\Tests\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    private $client = null;
    private $router = null;
        
    public function setUp()
    {
        $this->client = static::createClient();
        $this->router = $this->client->getContainer()->get('router');
    }
    
    public function testNoTweets()
    {
        $this->loadFixtures(array());
        
        $path = $this->router->generate('asynctweets_homepage');
        
        $crawler = $this->client->request('GET', $path);
        
        # <body>
        $this->assertEquals(1,
            $crawler->filter('html > body')->count());
        
        # Tweet
        $this->assertEquals(0,
            $crawler->filter(
                'main.container > div.tweets > div.media > blockquote.media-body'
            )->count());        
    }
    
    public function testTweets($path = null)
    {
        $this->loadFixtures(array(
            'AlexisLefebvre\Bundle\AsyncTweetsBundle\DataFixtures\ORM\LoadUserData',
            'AlexisLefebvre\Bundle\AsyncTweetsBundle\DataFixtures\ORM\LoadTweetData',
            'AlexisLefebvre\Bundle\AsyncTweetsBundle\DataFixtures\ORM\LoadMediaData',
        ));
        
        if (is_null($path))
        {
            $path = $this->router->generate('asynctweets_homepage');
        }
        
        $crawler = $this->client->request('GET', $path);
        
        # <body>
        $this->assertEquals(1,
            $crawler->filter('html > body')->count());
        
        # <title>
        $this->assertEquals(1,
            $crawler->filter('title:contains("Home timeline - since 565258739000049664 - AsyncTweets")')->count());
        
        # 2 navigation blocks
        $this->assertEquals(2,
            $crawler->filter('main.container > div.navigation')->count());
        
        # Tweet
        $this->assertEquals(1,
            $crawler->filter(
                'main.container > div.tweets > div.media > blockquote.media-body'
            )->count());
        
        # Link
        $this->assertEquals(1,
            $crawler->filter(
                'main.container > div.tweets > div.media > blockquote.media-body > '.
                'p > a'
            )->count());
        
        # TODO: Hashtags
        
        # Image
        $this->assertEquals(1,
            $crawler->filter('main.container > div.tweets > div.media')->count());
        
        $this->assertEquals(2,
            $crawler->filter(
                'blockquote.media-body > p')->count());
        
        # User
        $this->assertEquals(1,
            $crawler->filter(
                'main.container > div.tweets > div.media > blockquote.media-body > small > a:contains("Asynchronous tweets")'
            )->count());
    }
    
    public function testSinceTweetId()
    {
        $this->testTweets(
            $this->router->generate(
                'asynctweets_tweets_sinceTweetId',
                array('lastTweetId' => 565258739000049664)
            )
        );   
    }
    
    public function testOrderByUserSinceTweetId()
    {
        $this->testTweets(
            $this->router->generate(
                'asynctweets_tweets_orderByUser_sinceTweetId',
                array('lastTweetId' => 565258739000049664)
            )
        );        
    }
    
    public function testResetCookie()
    {
        $this->loadFixtures(array(
            'AlexisLefebvre\Bundle\AsyncTweetsBundle\DataFixtures\ORM\LoadUserData',
            'AlexisLefebvre\Bundle\AsyncTweetsBundle\DataFixtures\ORM\LoadTweetData',
            'AlexisLefebvre\Bundle\AsyncTweetsBundle\DataFixtures\ORM\LoadMediaData',
        ));
        
        $path = $this->router->generate('asynctweets_tweets_reset_cookie');
        
        $this->client->followRedirects();
        
        $this->client->request('GET', $path);
        
        $cookieJar = $this->client->getCookieJar();
        
        # Test the cookie
        $this->assertEquals(
            565258739000049664,
            $cookieJar->get('lastTweetId')->getValue()
        );
        
        # Test the redirection
        $this->client->followRedirects(false);
        
        $this->client->request('GET', $path);
        
        $this->assertTrue($this->client->getResponse()->isRedirect());
    }
}
