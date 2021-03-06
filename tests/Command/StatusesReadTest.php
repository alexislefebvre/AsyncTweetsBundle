<?php

namespace Acme\Command;

use AlexisLefebvre\Bundle\AsyncTweetsBundle\Command\StatusesReadCommand;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Component\Console\Tester\CommandTester;

class StatusesReadTest extends StatusesBase
{
    use FixturesTrait;

    /** @var CommandTester */
    public $commandTester;

    public function setUp(): void
    {
        parent::setUp();

        $this->application->add(new StatusesReadCommand());

        $command = $this->application->find('statuses:read');
        $this->commandTester = new CommandTester($command);
    }

    public function testStatusesReadEmpty()
    {
        $this->loadFixtures([]);

        // Disable decoration for tests on Windows
        $options = [];

        // http://stackoverflow.com/questions/5879043/php-script-detect-whether-running-under-linux-or-windows/5879078#5879078
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            // https://tracker.phpbb.com/browse/PHPBB3-12752
            $options['decorated'] = false;
        }

        $this->commandTester->execute([], $options);

        $this->assertStringContainsString('Current page: 1', $this->commandTester->getDisplay());
    }

    public function testStatusesReadWithTweets()
    {
        $this->loadFixtures([
            'Acme\DataFixtures\ORM\LoadTweetData',
        ]);

        // Disable decoration for tests on Windows
        $options = [];

        // http://stackoverflow.com/questions/5879043/php-script-detect-whether-running-under-linux-or-windows/5879078#5879078
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            // https://tracker.phpbb.com/browse/PHPBB3-12752
            $options['decorated'] = false;
        }

        $this->commandTester->execute([], $options);

        $display = $this->commandTester->getDisplay();

        $this->assertStringContainsString('Current page: 1', $display);

        // Test the first line of the table
        $this->assertStringContainsString(
            '| Asynchronous  | '.
                'Hello Twitter! #myfirstTweet             | '.
                '2015-02-10 21:19 |',
            $display
        );

        // Test the retweet
        $this->assertStringContainsString(
            '| GitHub        | '.
                'RT @GitHubEng: Cross-platform UI in      | '.
                '2015-08-20 17:00 |',
            $display
        );
    }
}
