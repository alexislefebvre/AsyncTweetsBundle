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
    
    public function testStatusesHomeTimeline()
    {
        $this->loadFixtures(array());
        
        $this->commandTester->execute(array());
        
        $display = $this->commandTester->getDisplay();
        
        $this->assertContains('[code] => 215', $display);
        $this->assertContains('[message] => Bad Authentication data.', $display);
    }
    
    public function testStatusesHomeTimelineEmpty()
    {
        $this->loadFixtures(array());
        
        $this->commandTester->execute(array(
            '--test' => true
        ));
        
        $this->assertContains('Number of tweets: 3', $this->commandTester->getDisplay());
    }
    
    public function testStatusesHomeTimelineNotArray()
    {
        $this->loadFixtures(array());
        
        $this->commandTester->execute(array(
            '--notarray' => true
        ));
        
        $this->assertContains('Something went wrong, $content is not an array.', $this->commandTester->getDisplay());
    }
    
    public function testStatusesHomeTimelineEmptyArray()
    {
        $this->loadFixtures(array());
        
        $this->commandTester->execute(array(
            '--emptyarray' => true
        ));
        
        $display = $this->commandTester->getDisplay();
        
        $this->assertContains('No new tweet.', $display);
    }
    
    public function testStatusesHomeTimelineWithTweets()
    {
        $this->loadFixtures(array(
            'AlexisLefebvre\Bundle\AsyncTweetsBundle\DataFixtures\ORM\LoadUserData',
            'AlexisLefebvre\Bundle\AsyncTweetsBundle\DataFixtures\ORM\LoadTweetData',
            'AlexisLefebvre\Bundle\AsyncTweetsBundle\DataFixtures\ORM\LoadMediaData',
        ));
        
        $this->commandTester->execute(array(
            '--table' => true,
            '--test' => true
        ));
        
        $display = $this->commandTester->getDisplay();
        
        $this->assertContains('Number of tweets: 3', $display);
        
        # Test the headers of the table
        $this->assertContains(
            '| Datetime            | '.
                'Text excerpt                        | '.
                'Name                |',
            $display
        );
        
        # Test the lines of the table
        $this->assertContains(
            '| 2015-02-10 21:18:00 | '.
                'Bonjour Twitter ! #monpremierTweet  | '.
                'Asynchronous tweets |',
            $display
        );
        $this->assertContains(
            '| 2015-02-10 21:19:20 | '.
                'Hello Twitter! #myfirstTweet        | '.
                'Asynchronous tweets |',
            $display
        );
        $this->assertContains(
            '| 2015-02-18 00:01:14 | '.
                '#image #test http://t.co/rX1oieH1ug | '.
                'Asynchronous tweets |',
            $display
        );
    }
}
