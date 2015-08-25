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
        
        $this->commandTester->execute(array());

        $this->assertContains('Current page: 1', $this->commandTester->getDisplay());
    }
    
    public function testStatusesReadWithTweets()
    {
        $this->loadFixtures(array(
            'AlexisLefebvre\Bundle\AsyncTweetsBundle\DataFixtures\ORM\LoadUserData',
            'AlexisLefebvre\Bundle\AsyncTweetsBundle\DataFixtures\ORM\LoadTweetData',
            'AlexisLefebvre\Bundle\AsyncTweetsBundle\DataFixtures\ORM\LoadMediaData',
        ));
        
        $this->commandTester->execute(array());
        
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
                '2015-08-19 17:00 |',
            $display
        );
    }
}
