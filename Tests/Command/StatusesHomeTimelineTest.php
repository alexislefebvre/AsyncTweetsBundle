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
        
        $this->assertContains('Number of tweets: 4', $this->commandTester->getDisplay());
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
        $this->loadFixtures(array());
        
        // Disable decoration for tests on Windows
        $options = array();
        
        // http://stackoverflow.com/questions/5879043/php-script-detect-whether-running-under-linux-or-windows/5879078#5879078
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            // https://tracker.phpbb.com/browse/PHPBB3-12752
            $options['decorated'] = false;
        }
        
        $this->commandTester->execute(
            array(
                '--table' => true,
                '--test' => true
            ),
            $options
        );
        
        $display = $this->commandTester->getDisplay();
        
        $this->assertContains('Number of tweets: 4', $display);
        
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
        
        // Test the retweet
        $this->assertContains(
            '| 2015-03-03 21:18:00 | '.
                'RT This is a retweet.               | '.
                'Asynchronous tweets |',
            $display
        );
    }
    
    public function testStatusesHomeTimelineWithSinceIdParameter()
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
        
        $this->commandTester->execute(
            array(
                '--emptyarray' => true
            ),
            $options
        );
        
        $display = $this->commandTester->getDisplay();
        
        $this->assertContains(
            'since_id parameter = '.
                ((PHP_INT_SIZE === 8) ? 634047285240926208 : 1005868289),
            $display
        );
    }
}
