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
        $this->assertEquals(2,
            $crawler->filter(
                'main.container > div.tweets > div.media > blockquote.media-body'
            )->count());
        
        # Link
        $this->assertEquals(2,
            $crawler->filter(
                'main.container > div.tweets > div.media > blockquote.media-body > '.
                'p > a'
            )->count());
        
        # TODO: Hashtags
        
        # Image
        $this->assertEquals(1,
            $crawler->filter('main.container > div.tweets blockquote.media-body > '.
                'p > a > img')->count());
        
        $this->assertEquals(3,
            $crawler->filter(
                'blockquote.media-body > p')->count());
        
        # User
        $this->assertEquals(2,
            $crawler->filter(
                'main.container > div.tweets > div.media > blockquote.media-body > small > a:contains("Asynchronous tweets")'
            )->count());
    }
    
    public function testSinceTweetId()
    {
        $this->testTweets(
            $this->router->generate(
                'asynctweets_tweets_sinceTweetId',
                array('firstTweetId' => 565258739000049664)
            )
        );
    }
    
    public function testTweetsPages()
    {
        $this->loadFixtures(array(
            'AlexisLefebvre\Bundle\AsyncTweetsBundle\DataFixtures\ORM\LoadUserData',
            'AlexisLefebvre\Bundle\AsyncTweetsBundle\DataFixtures\ORM\LoadTweetPagesData',
            'AlexisLefebvre\Bundle\AsyncTweetsBundle\DataFixtures\ORM\LoadMediaData',
        ));
        
        $path = $this->router->generate(
            'asynctweets_tweets_sinceTweetId',
            array('firstTweetId' => 15)
        );
        
        $crawler = $this->client->request('GET', $path);
        
        # <title>
        $this->assertEquals(1,
            $crawler->filter('title:contains("Home timeline - since 15 - AsyncTweets")')->count());
        
        # Tweet
        $this->assertEquals(10,
            $crawler->filter(
                'main.container > div.tweets > div.media > blockquote.media-body'
            )->count());
        
        $this->assertEquals(10,
            $crawler->filter(
                'blockquote.media-body > p')->count());
        
        # User
        $this->assertEquals(10,
            $crawler->filter(
                'main.container > div.tweets > div.media > blockquote.media-body > small > a:contains("Asynchronous tweets")'
            )->count());
        
        # Test previous and next page
        $previousPage = $crawler->filter('main.container > div.navigation:first-child '.
            '> div > ul > li:first-child > a')
            ->attr('href');
        
        $nextPage = $crawler->filter('main.container > div.navigation:first-child '.
            '> div > ul > li:last-child > a')
            ->attr('href');
        
        # Previous page
        $crawler = $this->client->request('GET', $previousPage);
        
        # Tweet
        $this->assertEquals(
            10,
            $crawler->filter(
                'main.container > div.tweets > div.media > blockquote.media-body'
            )->count()
            );
        
        
        # Next page
        $crawler = $this->client->request('GET', $nextPage);
        
        # Tweet
        $this->assertEquals(
            6,
            $crawler->filter(
                'main.container > div.tweets > div.media > blockquote.media-body'
            )->count()
            );
    }
    
    public function testCookie()
    {
        $this->loadFixtures(array(
            'AlexisLefebvre\Bundle\AsyncTweetsBundle\DataFixtures\ORM\LoadUserData',
            'AlexisLefebvre\Bundle\AsyncTweetsBundle\DataFixtures\ORM\LoadTweetData',
            'AlexisLefebvre\Bundle\AsyncTweetsBundle\DataFixtures\ORM\LoadMediaData',
        ));
        
        $tweetId = 565258739000049664;
        
        $path = $this->router->generate(
            'asynctweets_tweets_sinceTweetId',
            array('firstTweetId' => $tweetId)
        );
        
        $this->client->request('GET', $path);
        
        # Test the cookie
        $cookieJar = $this->client->getCookieJar();
        
        $this->assertEquals(
            $tweetId,
            $cookieJar->get('lastTweetId')->getValue()
        );
        
        # Display next tweet
        $nextTweetId = 567836201210900500;
         
        $path = $this->router->generate(
            'asynctweets_tweets_sinceTweetId',
            array('firstTweetId' => $nextTweetId)
        );
        
        $this->client->request('GET', $path);
        
        # Test that the cookie has been updated to the second tweet in
        #  the database (but first on this page)
        $cookieJar = $this->client->getCookieJar();
        
        $this->assertEquals(
            $nextTweetId,
            $cookieJar->get('lastTweetId')->getValue()
        );
        
        # Reset the cookie
        $path = $this->router->generate('asynctweets_tweets_reset_cookie');
        
        $this->client->followRedirects();
        
        $this->client->request('GET', $path);
        
        $cookieJar = $this->client->getCookieJar();
        
        # Test that the cookie is now the first tweet
        $this->assertEquals(
            $tweetId,
            $cookieJar->get('lastTweetId')->getValue()
        );
        
        # Test the redirection
        $this->client->followRedirects(false);
        
        $this->client->request('GET', $path);
        
        $this->assertTrue($this->client->getResponse()->isRedirect());
    }
}
