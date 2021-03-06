<?php

namespace Acme\Command;

use AlexisLefebvre\Bundle\AsyncTweetsBundle\Entity\Tweet;
use Doctrine\Common\Annotations\Annotation\IgnoreAnnotation;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @IgnoreAnnotation("requires")
 */
class StatusesHomeTimelineTest extends StatusesBase
{
    use FixturesTrait;

    /** @var CommandTester */
    public $commandTester;

    public function setUp(): void
    {
        parent::setUp();

        $this->application->add(new StatusesHomeTimelineTestCommand());

        $command = $this->application->find('statuses:hometimelinetest');
        $this->commandTester = new CommandTester($command);
    }

    public function testStatusesHomeTimeline()
    {
        $this->loadFixtures([]);

        $this->commandTester->execute([
            'test' => null,
        ]);

        $display = $this->commandTester->getDisplay();

        $this->assertStringContainsString('[code] => 215', $display);
        $this->assertStringContainsString('[message] => Bad Authentication data.', $display);
    }

    public function testStatusesHomeTimelineEmpty()
    {
        $this->loadFixtures([]);

        $this->commandTester->execute([
            'test' => 'json',
        ]);

        $this->assertStringContainsString('Number of tweets: 4', $this->commandTester->getDisplay());
    }

    public function testStatusesHomeTimelineNotArray()
    {
        $this->loadFixtures([]);

        $this->commandTester->execute([
            'test' => 'not_array',
        ]);

        $this->assertStringContainsString('Something went wrong, $content is not an array.', $this->commandTester->getDisplay());
    }

    public function testStatusesHomeTimelineEmptyArray()
    {
        $this->loadFixtures([]);

        $this->commandTester->execute([
            'test' => 'empty_array',
        ]);

        $display = $this->commandTester->getDisplay();

        $this->assertStringContainsString('No new tweet.', $display);
    }

    public function testStatusesHomeTimelineWithTweets()
    {
        $this->loadFixtures([]);

        // Disable decoration for tests on Windows
        $options = [];

        // http://stackoverflow.com/questions/5879043/php-script-detect-whether-running-under-linux-or-windows/5879078#5879078
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            // https://tracker.phpbb.com/browse/PHPBB3-12752
            $options['decorated'] = false;
        }

        $this->commandTester->execute(
            [
                'test'    => 'json',
                '--table' => true,
            ],
            $options
        );

        $display = $this->commandTester->getDisplay();

        $this->assertStringContainsString('Number of tweets: 4', $display);

        // Test the headers of the table
        $this->assertStringContainsString(
            '| Datetime            | '.
                'Text excerpt                        | '.
                'Name                |',
            $display
        );

        // Test the lines of the table
        $this->assertStringContainsString(
            '| 2015-02-10 21:18:00 | '.
                'Bonjour Twitter ! #monpremierTweet  | '.
                'Asynchronous tweets |',
            $display
        );
        $this->assertStringContainsString(
            '| 2015-02-10 21:19:20 | '.
                'Hello Twitter! #myfirstTweet        | '.
                'Asynchronous tweets |',
            $display
        );
        $this->assertStringContainsString(
            '| 2015-02-18 00:01:14 | '.
                '#image #test http://t.co/rX1oieH1ug | '.
                'Asynchronous tweets |',
            $display
        );

        // Test the retweet
        $this->assertStringContainsString(
            '| 2015-03-03 21:18:00 | '.
                'RT This is a retweet.               | '.
                'Asynchronous tweets |',
            $display
        );

        // Fetch tweet from database
        $em = $this
            ->getContainer()->get('doctrine.orm.entity_manager');

        $tweets = $em
            ->getRepository(Tweet::class)
            ->findAll();

        $this->assertSame(
            5,
            count($tweets)
        );
    }

    public function testStatusesHomeTimelineWithTweetAndRetweet()
    {
        $this->loadFixtures([]);

        // Disable decoration for tests on Windows
        $options = [];

        // http://stackoverflow.com/questions/5879043/php-script-detect-whether-running-under-linux-or-windows/5879078#5879078
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            // https://tracker.phpbb.com/browse/PHPBB3-12752
            $options['decorated'] = false;
        }

        $this->commandTester->execute(
            [
                'test'    => 'json_with_retweet',
                '--table' => true,
            ],
            $options
        );

        $display = $this->commandTester->getDisplay();

        $this->assertStringContainsString('Number of tweets: 1', $display);

        // Test the headers of the table
        $this->assertStringContainsString(
            '| Datetime            | '.
                'Text excerpt                             | '.
                'Name                |',
            $display
        );

        // Test the retweet
        $this->assertStringContainsString(
            '| 2015-08-22 20:20:27 | '.
                'RT @travisci: Good morning! We shipped o | '.
                'Asynchronous tweets |',
            $display
        );

        // Fetch tweet from database
        $em = $this
            ->getContainer()->get('doctrine.orm.entity_manager');

        $tweet = $em
            ->getRepository(Tweet::class)
            ->findOneBy([
                'id' => 999080449,
            ]);

        $this->assertSame(
            999080449,
            $tweet->getId()
        );

        // Image URL was stored with HTTPS
        $this->assertSame(
            'https://abs.twimg.com/sticky/default_profile_images/default_profile_5_normal.png',
            $tweet->getUser()->getProfileImageUrlHttpOrHttps()
        );

        // Image URL with HTTP was not stored
        $this->assertSame(
            'https://abs.twimg.com/sticky/default_profile_images/default_profile_5_normal.png',
            $tweet->getUser()->getProfileImageUrlHttps()
        );

        $this->assertNull(
            $tweet->getUser()->getProfileImageUrl()
        );

        // The number of retweet is the same for both tweets
        $this->assertSame(
            89,
            $tweet->getRetweetCount()
        );

        $this->assertSame(
            42,
            $tweet->getFavoriteCount()
        );

        $retweet = $tweet->getRetweetedStatus();

        $this->assertSame(
            79172609,
            $retweet->getId()
        );

        // The number of retweet is the same for both tweets
        $this->assertSame(
            89,
            $retweet->getRetweetCount()
        );

        $this->assertSame(
            61,
            $retweet->getFavoriteCount()
        );

        $tweets = $em
            ->getRepository(Tweet::class)
            ->findAll();

        $this->assertSame(
            2,
            count($tweets)
        );
    }

    /**
     * @requires OS Linux
     *
     * It returns “last tweet = 928032273747800064” on Windows (AppVeyor),
     * this is equal to 928032273747795968+4096, it looks like a bug on Windows.
     */
    public function testStatusesHomeTimelineWithSinceIdParameter()
    {
        $this->loadFixtures([
            'Acme\DataFixtures\ORM\LoadTweetData',
        ]);

        // Disable decoration for tests on Windows
        $options = [];

        $this->commandTester->execute(
            ['test' => 'empty_array'],
            $options
        );

        $display = $this->commandTester->getDisplay();

        $this->assertStringContainsString(
            'last tweet = 928032273747795968',
            $display
        );
    }
}
