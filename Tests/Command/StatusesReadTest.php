<?php

namespace AlexisLefebvre\Bundle\AsyncTweetsBundle\Tests\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

use AlexisLefebvre\Bundle\AsyncTweetsBundle\Command\StatusesReadCommand;

class StatusesReadTest extends StatusesBase
{
    public $commandTester;
    
    public function setUp()
    {
        parent::setUp();
        
        $this->application->add(new StatusesReadCommand());

        $command = $this->application->find('statuses:read');
        $this->commandTester = new CommandTester($command);
    }
    
    public function testStatusesReadEmpty()
    {
        $this->loadFixtures(array());
        
        // Disable decoration for tests on Windows
        $options = array();
        
        // http://stackoverflow.com/questions/5879043/php-script-detect-whether-running-under-linux-or-windows/5879078#5879078
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            // https://tracker.phpbb.com/browse/PHPBB3-12752
            $options['decorated'] = false;
        }
        
        $this->commandTester->execute(array(), $options);

        $this->assertContains('Current page: 1', $this->commandTester->getDisplay());
    }
    
    public function testStatusesReadWithTweets()
    {
        $this->loadFixtures(array(
            'AlexisLefebvre\Bundle\AsyncTweetsBundle\DataFixtures\ORM\LoadUserData',
            'AlexisLefebvre\Bundle\AsyncTweetsBundle\DataFixtures\ORM\LoadTweetData',
            'AlexisLefebvre\Bundle\AsyncTweetsBundle\DataFixtures\ORM\LoadMediaData',
        ));
        
        // Disable decoration for tests on Windows
        $options = array();
        
        // http://stackoverflow.com/questions/5879043/php-script-detect-whether-running-under-linux-or-windows/5879078#5879078
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            // https://tracker.phpbb.com/browse/PHPBB3-12752
            $options['decorated'] = false;
        }
        
        $this->commandTester->execute(array(), $options);
        
        $display = $this->commandTester->getDisplay();
        
        $this->assertContains('Current page: 1', $display);
        
        # Test the first line of the table
        $this->assertContains(
            '| Asynchronous  | '.
                'Hello Twitter! #myfirstTweet             | '.
                '2015-02-10 21:19 |',
            $display
        );
        
        // Test the retweet
        $this->assertContains(
            '| GitHub        | '.
                'RT @GitHubEng: Cross-platform UI in      | '.
                '2015-08-20 17:00 |',
            $display
        );
    }
}
