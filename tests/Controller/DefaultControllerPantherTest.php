<?php

namespace Acme\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Component\Panther\Client as PantherClient;
use Symfony\Component\Panther\PantherTestCaseTrait;

class DefaultControllerPantherTest extends WebTestCase
{
    use FixturesTrait;
    use PantherTestCaseTrait;

    /** @var PantherClient */
    private $testClient = null;

    public function setUp(): void
    {
        $this->testClient = self::createPantherClient();
    }

    public function testTweetsPanther($path = null)
    {
        $this->markTestSkipped('Panther is broken on CI for now');

        $this->loadFixtures([
            'Acme\DataFixtures\ORM\LoadTweetData',
        ]);

        if (is_null($path)) {
            $path = '/';
        }

        $crawler = $this->testClient->request('GET', $path);

        // <body>
        $this->assertSame(
            1,
            $crawler->filter('html > body')->count()
        );

        // <title>
        $this->assertStringContainsString(
            '<title>Home timeline - since 49664 - AsyncTweets</title>',
            $crawler->filter('title')->html()
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
                'blockquote.media-body > p'
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

        // Pending tweets
        $this->assertStringContainsString(
            '3 pending tweets',
            $crawler->filter('html > body')->text()
        );

        // Click on “Delete old tweets”
//        $this->testClient->executeScript("document.querySelector('a#tweets-delete').click()");
//        $this->testClient->getWebDriver()->switchTo()->alert()->accept();
//        $this->testClient->getWebDriver()->switchTo();
//
//        $crawler = $this->testClient->getCrawler();
//
//        // There are less pending tweets
//        $this->assertStringContainsString(
//            '1 pending tweets',
//            $crawler->filter('html > body')->text()
//        );
    }
}
