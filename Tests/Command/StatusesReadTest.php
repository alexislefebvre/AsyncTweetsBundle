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

        $this->assertRegExp('/Current page: 1/', $this->commandTester->getDisplay());
    }
    
    public function testStatusesReadWithTweets()
    {
        $this->loadFixtures(array(
            'AsyncTweetsBundle\DataFixtures\ORM\LoadUserData',
            'AsyncTweetsBundle\DataFixtures\ORM\LoadTweetData',
            'AsyncTweetsBundle\DataFixtures\ORM\LoadMediaData',
        ));
        
        $this->commandTester->execute(array());
        
        $display = $this->commandTester->getDisplay();
        
        $this->assertRegExp('/Current page: 1/', $display);
        
        # Test the first line of the table
        $this->assertRegExp(
            '/| Asynchronous  | '.
                'Hello Twitter! #myfirstTweet             | '.
                '2015-02-10 21:19 |/',
            $display
        );
    }
}
