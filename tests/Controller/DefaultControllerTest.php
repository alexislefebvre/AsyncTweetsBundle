<?php

namespace Acme\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use Liip\TestFixturesBundle\Test\FixturesTrait;

class DefaultControllerTest extends WebTestCase
{
    use FixturesTrait;

    private $testClient = null;

    public function setUp(): void
    {
        $this->testClient = static::makeClient();
    }

    public function testNoTweets()
    {
        $this->loadFixtures([]);

        $path = '/';

        $crawler = $this->testClient->request('GET', $path);

        // <body>
        $this->assertSame(1,
            $crawler->filter('html > body')->count());

        // Tweet
        $this->assertSame(0,
            $crawler->filter(
                'main.container > div.tweets > div.media > blockquote.media-body'
            )->count());
    }

    public function testTweets($path = null)
    {
        $this->loadFixtures([
            'Acme\DataFixtures\ORM\LoadTweetData',
        ]);

        if (is_null($path)) {
            $path = '/';
        }

        $this->testClient->enableProfiler();

        $crawler = $this->testClient->request('GET', $path);

        $this->assertStatusCode(200, $this->testClient);

        if ($profile = $this->testClient->getProfile()) {
            $this->assertSame(5,
                $profile->getCollector('db')->getQueryCount());
        } else {
            $this->markTestIncomplete(
                'Profiler is disabled.'
            );
        }

        // <body>
        $this->assertSame(
            1,
            $crawler->filter('html > body')->count()
        );

        // <title>
        $this->assertStringContainsString(
            'Home timeline - since 49664 - AsyncTweets',
            $crawler->filter('title')->text(),
            $crawler->filter('html')->text()
        );

        // 2 navigation blocks
        $this->assertSame(
            2,
            $crawler->filter('main.container > div.navigation')->count()
        );

        // Tweet
        $this->assertSame(
            3,
            $crawler->filter(
                'main.container > div.tweets > div.media > blockquote.media-body'
            )->count()
        );

        // Link
        $this->assertSame(
            2,
            $crawler->filter(
                'main.container > div.tweets > div.media > blockquote.media-body > '.
                'p > a'
            )->count()
        );

        // Images
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

        // User
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
        $this->testTweets('/sinceId/49664');
    }

    public function testTweetsPages()
    {
        $this->loadFixtures([
            'Acme\DataFixtures\ORM\LoadTweetPagesData',
        ]);

        $path = '/sinceId/15';

        $this->testClient->enableProfiler();

        $crawler = $this->testClient->request('GET', $path);

        $this->assertStatusCode(200, $this->testClient);

        if ($profile = $this->testClient->getProfile()) {
            $this->assertSame(4,
                $profile->getCollector('db')->getQueryCount());
        } else {
            $this->markTestIncomplete(
                'Profiler is disabled.'
            );
        }

        // <title>
        $this->assertStringContainsString(
            'Home timeline - since 15 - AsyncTweets',
            $crawler->filter('title')->text()
        );

        // Number of displayed Tweets
        $this->assertSame(
            5,
            $crawler->filter(
                'main.container > div.tweets > div.media > blockquote.media-body'
            )->count()
        );

        $this->assertSame(
            5,
            $crawler->filter(
                'blockquote.media-body > p')->count()
        );

        // User
        $this->assertSame(
            5,
            $crawler->filter(
                'main.container > div.tweets > div.media > blockquote.media-body > small > a:contains("Asynchronous tweets")'
            )->count()
        );

        // Test previous and next page
        $previousPage = $crawler->filter('main.container > div.navigation:first-child '.
            '> div > ul > li:first-child > a')
            ->attr('href');

        $nextPage = $crawler->filter('main.container > div.navigation:first-child '.
            '> div > ul > li:last-child > a')
            ->attr('href');

        // Previous page
        $crawler = $this->testClient->request('GET', $previousPage);

        // Tweet
        $this->assertSame(
            5,
            $crawler->filter(
                'main.container > div.tweets > div.media > blockquote.media-body'
            )->count()
            );

        // Next page
        $crawler = $this->testClient->request('GET', $nextPage);

        // Tweet
        $this->assertSame(
            5,
            $crawler->filter(
                'main.container > div.tweets > div.media > blockquote.media-body'
            )->count()
        );

        // "Mark as read" link
        $this->assertSame(
            5,
            $crawler->filter(
                'main.container > div.tweets > div.media > '.
                    'blockquote.media-body > small > '.
                    'a:contains("Mark as read")'
            )->count()
        );

        // Click on the link
        $link = $crawler->filter(
            'main.container > div.tweets > div.media > '.
                'blockquote.media-body > small > a:last-child'
            )
            ->eq(4)->link();

        $crawler = $this->testClient->click($link);

        // Tweet
        $this->assertSame(
            5,
            $crawler->filter(
                'main.container > div.tweets > div.media > blockquote.media-body'
            )->count()
        );

        // Go to last page
        $path = '/sinceId/38';

        $crawler = $this->testClient->request('GET', $path);

        // Tweet
        $this->assertSame(
            3,
            $crawler->filter(
                'main.container > div.tweets > div.media > blockquote.media-body'
            )->count()
        );

        // Number of pending tweets
        $this->assertStringContainsString(
            '3 pending tweets',
            $crawler->filter('main.container > div.navigation')
                ->first()->filter('div.alert-info')->text()
        );

        // Go to first page
        $path = '/';

        $crawler = $this->testClient->request('GET', $path);

        // Tweet
        $this->assertSame(
            5,
            $crawler->filter(
                'main.container > div.tweets > div.media > blockquote.media-body'
            )->count()
        );

        // Number of pending tweets
        $this->assertStringContainsString(
            '3 pending tweets',
            $crawler->filter('main.container > div.navigation')
                ->first()->filter('div.alert-info')->text()
        );
    }

    public function testCookie()
    {
        $this->loadFixtures([
            'Acme\DataFixtures\ORM\LoadTweetData',
        ]);

        $tweetId = 49664;

        $path = '/sinceId/'.$tweetId;

        $this->testClient->enableProfiler();

        $this->testClient->request('GET', $path);

        $this->assertStatusCode(200, $this->testClient);

        if ($profile = $this->testClient->getProfile()) {
            $this->assertSame(5,
                $profile->getCollector('db')->getQueryCount());
        } else {
            $this->markTestIncomplete(
                'Profiler is disabled.'
            );
        }

        // Test the cookie
        $cookieJar = $this->testClient->getCookieJar();

        $this->assertNotNull(
            $cookieJar
        );

        $this->assertNotNull(
            $cookieJar->get('lastTweetId')
        );

        // Cookie jar stores string
        $this->assertSame(
            (string) $tweetId,
            $cookieJar->get('lastTweetId')->getValue()
        );

        // Display next tweet
        $nextTweetId = 1210900500;

        $path = '/sinceId/'.$nextTweetId;

        $this->testClient->request('GET', $path);

        // Test that the cookie has been updated to the second tweet in
        //  the database (but first on this page)
        $cookieJar = $this->testClient->getCookieJar();

        // Cookie jar stores string
        $this->assertSame(
            (string) $tweetId,
            $cookieJar->get('lastTweetId')->getValue()
        );

        // Reset the cookie
        $path = '/resetCookie';

        $this->testClient->followRedirects();

        $this->testClient->request('GET', $path);

        $cookieJar = $this->testClient->getCookieJar();

        // Test that the cookie is now the first tweet
        // Cookie jar stores string
        $this->assertSame(
            (string) $tweetId,
            $cookieJar->get('lastTweetId')->getValue()
        );

        // Test the redirection
        $this->testClient->followRedirects(false);

        $this->testClient->request('GET', $path);

        $this->assertTrue($this->testClient->getResponse()->isRedirect());
    }

    public function testDeleteTweets()
    {
        $this->loadFixtures([
            'Acme\DataFixtures\ORM\LoadTweetPagesData',
        ]);

        $path = '/sinceId/15';

        // Fetch tweet from database
        $em = $this
            ->getContainer()->get('doctrine.orm.entity_manager');

        $tweets = $em
            ->getRepository('AsyncTweetsBundle:Tweet')
            ->findAll();

        $this->assertSame(
            40,
            count($tweets)
        );

        $medias = $em
            ->getRepository('AsyncTweetsBundle:Media')
            ->findAll();

        $this->assertSame(
            2,
            count($medias)
        );

        $this->testClient->enableProfiler();

        $crawler = $this->testClient->request('GET', $path);

        $this->assertStatusCode(200, $this->testClient);

        if ($profile = $this->testClient->getProfile()) {
            $this->assertSame(4,
                $profile->getCollector('db')->getQueryCount());
        } else {
            $this->markTestIncomplete(
                'Profiler is disabled.'
            );
        }

        // Test that there is a previous page
        $this->assertSame(
            '',
            $crawler->filter('main.container > div.navigation:first-child '.
                '> div > ul.pagination > li:first-child')
                ->attr('class')
        );

        // Check that "disabled" class is not present
        $this->assertNotEquals(
            'disabled',
            $crawler->filter('main.container > div.navigation '.
                '> div > ul.pagination > li:first-child')
                ->attr('class')
        );

        $link = $crawler->filter('a#tweets-delete')->link();

        $this->testClient->followRedirects(true);

        $crawler = $this->testClient->click($link);

        // Count deleted tweets
        $this->assertStringContainsString(
            '13 tweets deleted.',
            $crawler->filter('div.alert.alert-success')->text()
        );

        // Test that there is no previous page
        // The flashbag add an element before "main.container > div.navigation"
        $this->assertSame(
            'disabled',
            $crawler->filter('main.container > div.navigation '.
                '> div > ul.pagination > li:first-child')
                ->attr('class')
        );

        // Deleting tweets should not remove Media associated to several
        //  Tweet
        // Check that there is a Media on Next page
        $link = $crawler
            ->filter('ul.pagination > li > a:contains("Next")')
            ->eq(0)
            ->link();

        $tweets = $em
            ->getRepository('AsyncTweetsBundle:Tweet')
            ->findAll();

        $this->assertSame(
            26,
            count($tweets)
        );

        $medias = $em
            ->getRepository('AsyncTweetsBundle:Media')
            ->findAll();

        $this->assertSame(
            1,
            count($medias)
        );

        $crawler = $this->testClient->click($link);

        $link = $crawler
            ->filter('ul.pagination > li > a:contains("Next")')
            ->eq(0)
            ->link();

        $crawler = $this->testClient->click($link);

        // Image
        $this->assertSame(1,
            $crawler->filter('main.container > div.tweets blockquote.media-body > '.
                'p > a > img')->count());

        // Delete the second Tweet in order to remove all the Media

        // Go to Next page
        $link = $crawler
            ->filter('ul.pagination > li > a:contains("Next")')
            ->eq(0)
            ->link();

        $tweets = $em
            ->getRepository('AsyncTweetsBundle:Tweet')
            ->findAll();

        $this->assertSame(
            26,
            count($tweets)
        );

        $medias = $em
            ->getRepository('AsyncTweetsBundle:Media')
            ->findAll();

        $this->assertSame(
            1,
            count($medias)
        );

        $crawler = $this->testClient->click($link);

        $link = $crawler
            ->filter('ul.pagination > li > a:contains("Next")')
            ->eq(0)
            ->link();

        $crawler = $this->testClient->click($link);

        $link = $crawler->filter('a#tweets-delete')->link();

        $crawler = $this->testClient->click($link);

        // Count deleted tweets
        $this->assertStringContainsString(
            '20 tweets deleted.',
            $crawler->filter('div.alert.alert-success')->text()
        );

        $tweets = $em
            ->getRepository('AsyncTweetsBundle:Tweet')
            ->findAll();

        $this->assertSame(
            6,
            count($tweets)
        );

        $medias = $em
            ->getRepository('AsyncTweetsBundle:Media')
            ->findAll();

        $this->assertSame(
            0,
            count($medias)
        );

        // Delete all the tweets except the last
        $path = '/sinceId/40';

        $crawler = $this->testClient->request('GET', $path);

        $link = $crawler->filter('a#tweets-delete')->link();

        $crawler = $this->testClient->click($link);

        // Count deleted tweets
        $this->assertStringContainsString(
            '5 tweets deleted.',
            $crawler->filter('div.alert.alert-success')->text()
        );

        $tweets = $em
            ->getRepository('AsyncTweetsBundle:Tweet')
            ->findAll();

        $this->assertSame(
            1,
            count($tweets)
        );

        $medias = $em
            ->getRepository('AsyncTweetsBundle:Media')
            ->findAll();

        $this->assertSame(
            0,
            count($medias)
        );
    }

    public function testHideRetweetedTweets()
    {
        $this->loadFixtures([
            'Acme\DataFixtures\ORM\LoadTweetAndRetweetData',
        ]);

        ////////// Homepage //////////
        $path = '/';

        // Fetch tweet from database
        $em = $this
            ->getContainer()->get('doctrine.orm.entity_manager');

        $tweets = $em
            ->getRepository('AsyncTweetsBundle:Tweet')
            ->findAll();

        $this->assertSame(
            5,
            count($tweets)
        );

        $retweeted_tweet = $em
            ->getRepository('AsyncTweetsBundle:Tweet')
            ->findOneBy(['id' => 20]);

        $this->assertTrue($retweeted_tweet->isInTimeline());

        $this->testClient->enableProfiler();

        $crawler = $this->testClient->request('GET', $path);

        $this->assertStatusCode(200, $this->testClient);

        if ($profile = $this->testClient->getProfile()) {
            $this->assertSame(4,
                $profile->getCollector('db')->getQueryCount());
        } else {
            $this->markTestIncomplete(
                'Profiler is disabled.'
            );
        }

        // Number of displayed Tweets
        $this->assertSame(
            5,
            $crawler->filter(
                'main.container > div.tweets > div.media > blockquote.media-body'
            )->count()
        );

        ////////// Click on the second "Mark as read" link //////////
        // One Tweet will be deleted (id = 10)
        $link = $crawler->filter('a:contains("Mark as read")')
            ->eq(1)->link();

        $this->testClient->followRedirects(true);

        $crawler = $this->testClient->click($link);

        $this->assertStringEndsWith(
            '/sinceId/20',
            $this->testClient->getRequest()->getUri()
        );

        // Number of displayed Tweets
        $this->assertSame(
            4,
            $crawler->filter(
                'main.container > div.tweets > div.media > blockquote.media-body'
            )->count()
        );

        // Delete old tweets
        $link = $crawler->filter('a#tweets-delete')->link();

        $crawler = $this->testClient->click($link);

        // Count deleted tweets
        $this->assertSame(
            1,
            $crawler->filter('div.alert.alert-success')->count(),
            $crawler->text()
        );

        $this->assertStringContainsString(
            '1 tweets deleted.',
            $crawler->filter('div.alert.alert-success')->text()
        );

        // Number of displayed Tweets
        $this->assertSame(
            4,
            $crawler->filter(
                'main.container > div.tweets > div.media > blockquote.media-body'
            )->count()
        );

        // Number of tweets
        $this->assertSame(
            4,
            count($em
                ->getRepository('AsyncTweetsBundle:Tweet')
                ->findAll())
        );

        $retweeted_tweet = $em
            ->getRepository('AsyncTweetsBundle:Tweet')
            ->findOneBy(['id' => 20]);

        $em->refresh($retweeted_tweet);

        $this->assertTrue($retweeted_tweet->isInTimeline());

        ////////// Click on the second "Mark as read" link //////////
        // One Tweet will be hidden
        $link = $crawler->filter('a:contains("Mark as read")')
            ->eq(1)->link();

        $crawler = $this->testClient->click($link);

        $this->assertStringEndsWith(
            '/sinceId/30',
            $this->testClient->getRequest()->getUri()
        );

        // Number of displayed Tweets
        $this->assertSame(
            3,
            $crawler->filter(
                'main.container > div.tweets > div.media > blockquote.media-body'
            )->count()
        );

        // Tweet has not been hidden
        $retweeted_tweet = $em
            ->getRepository('AsyncTweetsBundle:Tweet')
            ->findOneBy(['id' => 20]);

        $em->refresh($retweeted_tweet);

        $this->assertTrue($retweeted_tweet->isInTimeline());

        // Delete old tweets
        $link = $crawler->filter('a#tweets-delete')->link();

        $crawler = $this->testClient->click($link);

        // Tweet has been hidden
        $retweeted_tweet = $em
            ->getRepository('AsyncTweetsBundle:Tweet')
            ->findOneBy(['id' => 20]);

        $em->refresh($retweeted_tweet);

        $this->assertFalse($retweeted_tweet->isInTimeline());

        // Count deleted tweets
        $this->assertStringContainsString(
            '0 tweets deleted.',
            $crawler->filter('div.alert.alert-success')->text()
        );

        // Number of displayed Tweets
        $this->assertSame(
            3,
            $crawler->filter(
                'main.container > div.tweets > div.media > blockquote.media-body'
            )->count()
        );

        // The tweet has not been deleted
        $this->assertSame(
            4,
            count($em
                ->getRepository('AsyncTweetsBundle:Tweet')
                ->findAll())
        );

        ////////// Click on the second "Mark as read" link //////////
        $link = $crawler->filter('a:contains("Mark as read")')
            ->eq(1)->link();

        $crawler = $this->testClient->click($link);

        $this->assertStringEndsWith(
            '/sinceId/40',
            $this->testClient->getRequest()->getUri()
        );

        // Delete old tweets
        $link = $crawler->filter('a#tweets-delete')->link();

        $crawler = $this->testClient->click($link);

        // Number of displayed Tweets
        $this->assertSame(
            2,
            $crawler->filter(
                'main.container > div.tweets > div.media > blockquote.media-body'
            )->count()
        );

        // Count deleted tweets
        $this->assertStringContainsString(
            '1 tweets deleted.',
            $crawler->filter('div.alert.alert-success')->text()
        );

        // The tweet has been deleted
        $this->assertSame(
            2,
            count($em
                ->getRepository('AsyncTweetsBundle:Tweet')
                ->findAll())
        );

        // Click on the second "Mark as read" link
        $link = $crawler->filter('a:contains("Mark as read")')
            ->eq(1)->link();

        $crawler = $this->testClient->click($link);

        $this->assertStringEndsWith(
            '/sinceId/50',
            $this->testClient->getRequest()->getUri()
        );

        // Delete old tweets
        $link = $crawler->filter('a#tweets-delete')->link();

        $crawler = $this->testClient->click($link);

        // Number of displayed Tweets
        $this->assertSame(
            1,
            $crawler->filter(
                'main.container > div.tweets > div.media > blockquote.media-body'
            )->count()
        );

        // Count deleted tweets
        $this->assertStringContainsString(
            '1 tweets deleted.',
            $crawler->filter('div.alert.alert-success')->text()
        );

        $this->assertSame(
            1,
            count($em
                ->getRepository('AsyncTweetsBundle:Tweet')
                ->findAll())
        );
    }
}
