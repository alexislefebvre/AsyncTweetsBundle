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
        
        $path = '/';
        
        $crawler = $this->client->request('GET', $path);
        
        # <body>
        $this->assertSame(1,
            $crawler->filter('html > body')->count());
        
        # Tweet
        $this->assertSame(0,
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
            $path = '/';
        }
        
        $crawler = $this->client->request('GET', $path);
        
        # <body>
        $this->assertSame(
            1,
            $crawler->filter('html > body')->count()
        );
        
        # <title>
        $this->assertContains(
            'Home timeline - since '.
                ((PHP_INT_SIZE === 8) ? 565258739000049664 : 49664).
                ' - AsyncTweets',
            $crawler->filter('title')->text()
        );
        
        # 2 navigation blocks
        $this->assertSame(
            2,
            $crawler->filter('main.container > div.navigation')->count()
        );
        
        # Tweet
        $this->assertSame(
            3,
            $crawler->filter(
                'main.container > div.tweets > div.media > blockquote.media-body'
            )->count()
        );
        
        # Link
        $this->assertSame(
            2,
            $crawler->filter(
                'main.container > div.tweets > div.media > blockquote.media-body > '.
                'p > a'
            )->count()
        );
        
        # TODO: Hashtags
        
        # Images
        $this->assertSame(
            1,
            $crawler->filter('main.container > div.tweets blockquote.media-body > '.
                'p > a > img')->count()
        );
        
        $this->assertSame(
            4,
            $crawler->filter(
                'blockquote.media-body > p')->count()
        );
        
        # User
        $this->assertSame(
            2,
            $crawler->filter(
                'main.container > div.tweets > div.media > blockquote.media-body > small > a:contains("Asynchronous tweets")'
            )->count()
        );
        
        // Retweet
        $this->assertSame(
            1,
            $crawler->filter(
                'main.container > div.tweets > div.media > '.
                'blockquote.media-body > div.media > blockquote'
            )->count()
        );
        
        // Link
        $this->assertSame(
            1,
            $crawler->filter(
                'main.container > div.tweets > div.media > blockquote.media-body > '.
                'div.media > blockquote.media-body > '.
                'p > a'
            )->count()
        );
    }
    
    public function testSinceTweetId()
    {
        $this->testTweets(
            '/sinceId/'.
                ((PHP_INT_SIZE === 8) ? 565258739000049664 : 49664)
        );
    }
    
    public function testTweetsPages()
    {
        $this->loadFixtures(array(
            'AlexisLefebvre\Bundle\AsyncTweetsBundle\DataFixtures\ORM\LoadUserData',
            'AlexisLefebvre\Bundle\AsyncTweetsBundle\DataFixtures\ORM\LoadTweetPagesData',
            'AlexisLefebvre\Bundle\AsyncTweetsBundle\DataFixtures\ORM\LoadMediaData',
        ));
        
        $path = '/sinceId/15';
        
        $crawler = $this->client->request('GET', $path);
        
        # <title>
        $this->assertContains(
            'Home timeline - since 15 - AsyncTweets',
            $crawler->filter('title')->text()
        );
        
        # Tweet
        $this->assertSame(
            10,
            $crawler->filter(
                'main.container > div.tweets > div.media > blockquote.media-body'
            )->count()
        );
        
        $this->assertSame(
            10,
            $crawler->filter(
                'blockquote.media-body > p')->count()
        );
        
        # User
        $this->assertSame(
            10,
            $crawler->filter(
                'main.container > div.tweets > div.media > blockquote.media-body > small > a:contains("Asynchronous tweets")'
            )->count()
        );
        
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
        $this->assertSame(
            10,
            $crawler->filter(
                'main.container > div.tweets > div.media > blockquote.media-body'
            )->count()
            );
        
        
        # Next page
        $crawler = $this->client->request('GET', $nextPage);
        
        # Tweet
        $this->assertSame(
            10,
            $crawler->filter(
                'main.container > div.tweets > div.media > blockquote.media-body'
            )->count()
        );
        
        # "Mark as read" link
        $this->assertSame(
            10,
            $crawler->filter(
                'main.container > div.tweets > div.media > '.
                    'blockquote.media-body > small > '.
                    'a:contains("Mark as read")'
            )->count()
        );
        
        # Click on the link
        $link = $crawler->filter(
            'main.container > div.tweets > div.media > '.
                'blockquote.media-body > small > a:last-child'
            )
            ->eq(9)->link();
        
        $crawler = $this->client->click($link);
        
        # Tweet
        $this->assertSame(
            7,
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
        
        $tweetId = ((PHP_INT_SIZE === 8) ? 565258739000049664 : 49664);
        
        $path = '/sinceId/'.$tweetId;
        
        $this->client->request('GET', $path);
        
        # Test the cookie
        $cookieJar = $this->client->getCookieJar();
        
        $this->assertNotNull(
            $cookieJar
        );
        
        $this->assertNotNull(
            $cookieJar->get('lastTweetId')
        );
        
        $this->assertEquals(
            $tweetId,
            $cookieJar->get('lastTweetId')->getValue()
        );
        
        # Display next tweet
        $nextTweetId = ((PHP_INT_SIZE === 8) ? 567836201210900500 : 1210900500);
        
        $path = '/sinceId/'.$nextTweetId;
        
        $this->client->request('GET', $path);
        
        # Test that the cookie has been updated to the second tweet in
        #  the database (but first on this page)
        $cookieJar = $this->client->getCookieJar();
        
        $this->assertEquals(
            ((PHP_INT_SIZE === 8) ? 567836201210900500 : 49664),
            $cookieJar->get('lastTweetId')->getValue()
        );
        
        # Reset the cookie
        $path = '/resetCookie';
        
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
    
    public function testDeleteTweets()
    {
        $this->loadFixtures(array(
            'AlexisLefebvre\Bundle\AsyncTweetsBundle\DataFixtures\ORM\LoadUserData',
            'AlexisLefebvre\Bundle\AsyncTweetsBundle\DataFixtures\ORM\LoadTweetPagesData',
            'AlexisLefebvre\Bundle\AsyncTweetsBundle\DataFixtures\ORM\LoadMediaData',
        ));
        
        $path = '/sinceId/15';
        
        // Fetch tweet from database
        $em = $this
            ->getContainer()->get('doctrine.orm.entity_manager');
        
        $tweets = $em
            ->getRepository('AsyncTweetsBundle:Tweet')
            ->findAll();
        
        $this->assertEquals(
            40,
            count($tweets)
        );
        
        $medias = $em
            ->getRepository('AsyncTweetsBundle:Media')
            ->findAll();
        
        $this->assertEquals(
            2,
            count($medias)
        );
        
        $crawler = $this->client->request('GET', $path);
        
        # Test that there is a previous page
        $this->assertSame(
            '',
            $crawler->filter('main.container > div.navigation:first-child '.
                '> div > ul.pagination > li:first-child')
                ->attr('class')
        );
        
        # Check that "disabled" class is not present
        $this->assertNotEquals(
            'disabled',
            $crawler->filter('main.container > div.navigation '.
                '> div > ul.pagination > li:first-child')
                ->attr('class')
        );
        
        $link = $crawler->filter('a#tweets-delete')->link();
        
        $this->client->followRedirects(true);
        
        $crawler = $this->client->click($link);
        
        # Count deleted tweets
        $this->assertContains(
            '13 tweets deleted.',
            $crawler->filter('div.alert.alert-success')->text()
        );
        
        # Test that there is no previous page
        # The flashbag add an element before "main.container > div.navigation"
        $this->assertSame(
            'disabled',
            $crawler->filter('main.container > div.navigation '.
                '> div > ul.pagination > li:first-child')
                ->attr('class')
        );
        
        # Deleting tweets should not remove Media associated to several
        #  Tweet
        # Check that there is a Media on Next page
        $link = $crawler
            ->filter('ul.pagination > li > a:contains("Next")')
            ->eq(0)
            ->link()
        ;
        
        $tweets = $em
            ->getRepository('AsyncTweetsBundle:Tweet')
            ->findAll();
        
        $this->assertEquals(
            26,
            count($tweets)
        );
        
        $medias = $em
            ->getRepository('AsyncTweetsBundle:Media')
            ->findAll();
        
        $this->assertEquals(
            1,
            count($medias)
        );
        
        $crawler = $this->client->click($link);
        
        # Image
        $this->assertSame(1,
            $crawler->filter('main.container > div.tweets blockquote.media-body > '.
                'p > a > img')->count());
        
        # Delete the second Tweet in order to remove all the Media
        
        # Go to Next page
        $link = $crawler
            ->filter('ul.pagination > li > a:contains("Next")')
            ->eq(0)
            ->link()
        ;
        
        $tweets = $em
            ->getRepository('AsyncTweetsBundle:Tweet')
            ->findAll();
        
        $this->assertEquals(
            26,
            count($tweets)
        );
        
        $medias = $em
            ->getRepository('AsyncTweetsBundle:Media')
            ->findAll();
        
        $this->assertEquals(
            1,
            count($medias)
        );
        
        $crawler = $this->client->click($link);
        
        $link = $crawler->filter('a#tweets-delete')->link();
        
        $crawler = $this->client->click($link);
        
        # Count deleted tweets
        $this->assertContains(
            '20 tweets deleted.',
            $crawler->filter('div.alert.alert-success')->text()
        );
        
        $tweets = $em
            ->getRepository('AsyncTweetsBundle:Tweet')
            ->findAll();
        
        $this->assertEquals(
            6,
            count($tweets)
        );
        
        $medias = $em
            ->getRepository('AsyncTweetsBundle:Media')
            ->findAll();
        
        $this->assertEquals(
            0,
            count($medias)
        );
        
        // Delete all the tweets except the last
        $path = '/sinceId/40';
        
        $crawler = $this->client->request('GET', $path);
        
        $link = $crawler->filter('a#tweets-delete')->link();
        
        $crawler = $this->client->click($link);
        
        # Count deleted tweets
        $this->assertContains(
            '5 tweets deleted.',
            $crawler->filter('div.alert.alert-success')->text()
        );
        
        $tweets = $em
            ->getRepository('AsyncTweetsBundle:Tweet')
            ->findAll();
        
        $this->assertEquals(
            1,
            count($tweets)
        );
        
        $medias = $em
            ->getRepository('AsyncTweetsBundle:Media')
            ->findAll();
        
        $this->assertEquals(
            0,
            count($medias)
        );
    }
}
