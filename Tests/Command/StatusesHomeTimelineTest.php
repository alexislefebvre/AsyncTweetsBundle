<?php

namespace AlexisLefebvre\Bundle\AsyncTweetsBundle\Tests\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

use AlexisLefebvre\Bundle\AsyncTweetsBundle\Command\StatusesHomeTimelineCommand;

class StatusesHomeTimelineTest extends StatusesBase
{
    public $commandTester;
    
    public function setUp()
    {
        parent::setUp();
        
        $this->application->add(new StatusesHomeTimelineCommand());

        $command = $this->application->find('statuses:hometimeline');
        $this->commandTester = new CommandTester($command);
    }
    
    public function testStatusesHomeTimelineEmpty()
    {
        $this->loadFixtures(array());
        
        $this->commandTester->execute(array(
            '--test' => true
        ));
        
        $this->assertRegExp('/Number of tweets: 3/', $this->commandTester->getDisplay());
    }
    
    public function testStatusesHomeTimelineNotArray()
    {
        $this->loadFixtures(array());
        
        $this->commandTester->execute(array(
            '--notarray' => true
        ));
        
        $this->assertRegExp('/Something went wrong, \$content is not an array./', $this->commandTester->getDisplay());
    }
    
    public function testStatusesHomeTimelineEmptyArray()
    {
        $this->loadFixtures(array());
        
        $this->commandTester->execute(array(
            '--emptyarray' => true
        ));
        
        $display = $this->commandTester->getDisplay();
        
        $this->assertRegExp('/Number of tweets: 0/', $display);
        $this->assertRegExp('/No new tweet./', $display);
    }
    
    public function testStatusesHomeTimelineWithTweets()
    {
        $this->loadFixtures(array(
            'AsyncTweetsBundle\DataFixtures\ORM\LoadUserData',
            'AsyncTweetsBundle\DataFixtures\ORM\LoadTweetData',
            'AsyncTweetsBundle\DataFixtures\ORM\LoadMediaData',
        ));
        
        $this->commandTester->execute(array(
            '--table' => true,
            '--test' => true
        ));
        
        $display = $this->commandTester->getDisplay();
        
        $this->assertRegExp('/Number of tweets: 3/', $display);
        
        # Test the first line of the table
        $this->assertRegExp(
            '/| Wed Feb 18 00:01:14 +0000 2015 | '.
                '#image #test http:\/\/ | '.
                'Asynchronous tweets |/',
            $display
        );
        $this->assertRegExp('/(.*)Wed Feb 18 00:01:14 \+0000 2015(.*)/', $display);
    }
}
