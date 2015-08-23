<?php

namespace AlexisLefebvre\Bundle\AsyncTweetsBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;
use Abraham\TwitterOAuth\TwitterOAuth;
use AlexisLefebvre\Bundle\AsyncTweetsBundle\Entity\User;
use AlexisLefebvre\Bundle\AsyncTweetsBundle\Entity\Tweet;
use AlexisLefebvre\Bundle\AsyncTweetsBundle\Entity\Media;
use Symfony\Component\Console\Helper\Table;

class StatusesHomeTimelineCommand extends BaseCommand
{
    private $displayTable;
    private $table;
    private $progress;
    
    /** @see https://dev.twitter.com/rest/reference/get/statuses/home_timeline */
    private $parameters = array(
        'count' => 200,
        'exclude_replies' => true
    );
    
    protected function configure()
    {
        parent::configure();
        
        $this
            ->setName('statuses:hometimeline')
            ->setDescription('Fetch home timeline')
            # http://symfony.com/doc/2.3/cookbook/console/console_command.html#automatically-registering-commands
            ->addOption('table', null, InputOption::VALUE_NONE, 'Display a table with tweets')
            ->addOption('test', null, InputOption::VALUE_NONE, 'Read a tweet from a JSON file')
            ->addOption('notarray', null, InputOption::VALUE_NONE, 'Return null instead of JSON')
            ->addOption('emptyarray', null, InputOption::VALUE_NONE, 'Return an empty array instead of JSON')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->setAndDisplayLastTweet($output);
        
        $content = $this->getContent($input);
        
        if (! is_array($content))
        {
            $this->displayContentNotArrayError($output, $content);
            return 1;
        }
        
        $numberOfTweets = count($content);
        
        if ($numberOfTweets == 0)
        {
            $output->writeln('<comment>No new tweet.</comment>');
            return 0;
        }
        
        $this->addAndDisplayTweets($input, $output, $content, $numberOfTweets);
    }
    
    protected function setAndDisplayLastTweet(OutputInterface $output)
    {
        # Get the last tweet
        $lastTweet = $this->em
            ->getRepository('AsyncTweetsBundle:Tweet')
            ->getLastTweet();
        
        # And use it in the request if it exists
        if ($lastTweet)
        {
            $this->parameters['since_id'] = $lastTweet->getId();
            
            $comment = 'since_id parameter = '.$this->parameters['since_id'];
        }
        else
        {
            $comment = 'no since_id parameter';
        }
        
        $output->writeln('<comment>'.$comment.'</comment>');
    }
    
    protected function getTestContent()
    {
        /** @see https://insight.sensiolabs.com/what-we-analyse/symfony.dependency_injection.use_dir_file_constant */
        return(json_decode(file_get_contents(
            $this->container->get('kernel')->locateResource(
                '@AsyncTweetsBundle/Tests/Command/data/tweets.json'
            )
        )));
    }
    
    /**
     * @param InputInterface $input
     */
    protected function getContent(InputInterface $input)
    {
        if ($input->getOption('test'))
        {
            return($this->getTestContent());
        }
        else if ($input->getOption('notarray'))
        {
            return(null);
        }
        else if ($input->getOption('emptyarray'))
        {
            return(array());
        }
        else
        {
            return($this->getConnection());
        }
    }
    
    protected function getConnection()
    {
        $connection = new TwitterOAuth(
            $this->container->getParameter('twitter_consumer_key'),
            $this->container->getParameter('twitter_consumer_secret'),
            $this->container->getParameter('twitter_token'),
            $this->container->getParameter('twitter_token_secret')
        );
        
        return($connection->get(
            'statuses/home_timeline',
            $this->parameters
        ));
    }
    
    /**
     * @param OutputInterface $output
     * @param array $content
     */
    protected function displayContentNotArrayError(OutputInterface $output,
        $content)
    {
        $formatter = $this->getHelper('formatter');
        
        $errorMessages = array('Error!', 'Something went wrong, $content is not an array.');
        $formattedBlock = $formatter->formatBlock($errorMessages, 'error');
        $output->writeln($formattedBlock);
        $output->writeln(print_r($content, true));
    }
    
    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param array $content
     * @param integer $numberOfTweets
     */
    protected function addAndDisplayTweets(InputInterface $input,
        OutputInterface $output, $content, $numberOfTweets)
    {
        $output->writeln('<comment>Number of tweets: '.$numberOfTweets.'</comment>');
        
        # Iterate through $content in order to add the oldest tweet first, 
        #  if there is an error the oldest tweet will still be saved
        #  and newer tweets can be saved next time the command is launched
        $tweets = array_reverse($content);
        
        $this->setProgressBar($output, $numberOfTweets);
        $this->setTable($input, $output);
        $this->iterateTweets($tweets);
        $this->endProgressBar($output);
        $this->displayTable();
    }
    
    /**
     * @param OutputInterface $output
     * @param integer $numberOfTweets
     */
    protected function setProgressBar(OutputInterface $output,
        $numberOfTweets)
    {
        $this->progress = new ProgressBar($output, $numberOfTweets);
        $this->progress->setBarCharacter('<comment>=</comment>');
        $this->progress->start();
    }
    
    /**
     * @param InputInterface $input
     */
    protected function setTable(InputInterface $input,
        OutputInterface $output)
    {
        $this->displayTable = $input->getOption('table');
        
        # Display
        if ($this->displayTable)
        {
            $this->table = new Table($output);
            $this->table
                ->setHeaders(array('Datetime', 'Text excerpt', 'Name'))
            ;
        }
    }
    
    /**
     * @param array $tweets
     */
    protected function iterateTweets($tweets)
    {
        foreach ($tweets as $tweetTmp)
        {
            $this->addTweet($tweetTmp, true);
            
            $this->progress->advance();
        }
    }
    
    protected function endProgressBar($output)
    {
        $this->progress->finish();
        $output->writeln('');
    }
    
    protected function displayTable()
    {
        if ($this->displayTable)
        {
            $this->table->render();
        }
    }
    
    /**
     * @param \stdClass $userTmp
     */
    protected function persistUser(\stdClass $userTmp)
    {
        $user = $this->em
            ->getRepository('AsyncTweetsBundle:User')
            ->findOneById($userTmp->id)
        ;
        
        if (! $user)
        {
            # Only set the id when adding the User
            $user = new User($userTmp->id);
        }
        
        # Update other fields
        $user->setValues($userTmp);
        
        $this->em->persist($user);
        
        return $user;
    }
    
    /**
     * @param array $medias
     * @param Tweet $tweet
     */
    public function iterateMedias($medias, Tweet $tweet)
    {
        foreach ($medias as $mediaTmp)
        {
            if ($mediaTmp->type == 'photo')
            {
                $this->persistMedia($tweet, $mediaTmp);
            }
        }
    }
    
    /**
     * @param \stdClass $tweetTmp
     * @param Tweet $tweet
     */
    protected function addMedias(\stdClass $tweetTmp, Tweet $tweet)
    {
        if ((isset($tweetTmp->entities))
            && (isset($tweetTmp->entities->media)))
        {
            $this->iterateMedias($tweetTmp->entities->media, $tweet);
        }
    }
    
    /**
     * @param \stdClass $tweetTmp
     * @param User $user
     * @param boolean $inTimeline
     */
    protected function persistTweet(\stdClass $tweetTmp, User $user,
        $inTimeline)
    {
        $tweet = $this->em
            ->getRepository('AsyncTweetsBundle:Tweet')
            ->findOneById($tweetTmp->id)
        ;
        
        if (! $tweet)
        {
            $tweet = new Tweet($tweetTmp->id);
            $tweet->setValues($tweetTmp);
            $tweet->setUser($user);
            $tweet->setInTimeline($inTimeline);
            $this->addMedias($tweetTmp, $tweet);
        }
        
        if (isset($tweetTmp->retweeted_status))
        {
            $retweet = $this->em
                ->getRepository('AsyncTweetsBundle:Tweet')
                ->findOneById($tweetTmp->retweeted_status->id)
            ;
            
            if (! $retweet)
            {
                $retweet = $this->addTweet(
                    $tweetTmp->retweeted_status
                );
            }
            
            $tweet->setRetweetedStatus($retweet);
        }
        
        $this->em->persist($tweet);
        $this->em->flush();
        
        return $tweet;
    }
    
    /**
     * @param Tweet $tweet
     * @param \stdClass $mediaTmp
     */
    protected function persistMedia(Tweet $tweet, \stdClass $mediaTmp)
    {
        $media = $this->em
            ->getRepository('AsyncTweetsBundle:Media')
            ->findOneById($mediaTmp->id)
        ;
        
        if (! $media)
        {
            # Only set the id and values when adding the Media
            $media = new Media($mediaTmp->id);
            $media->setValues($mediaTmp);
            $this->em->persist($media);
            $this->em->flush();
        }
        
        $tweet->addMedia($media);
    }
    
    /**
     * @param \stdClass $tweetTmp
     * @param boolean $inTimeline
     */
    protected function addTweet(\stdClass $tweetTmp, $inTimeline = false)
    {
        $user = $this->persistUser($tweetTmp->user);
        
        $tweet = $this->persistTweet($tweetTmp, $user, $inTimeline);
        
        if ($this->displayTable)
        {
            $this->table->addRow(array(
                $tweet->getCreatedAt()->format('Y-m-d H:i:s'),
                mb_substr($tweet->getText(), 0, 40),
                $user->getName()
            ));
        }
        
        return $tweet;
    }
}
