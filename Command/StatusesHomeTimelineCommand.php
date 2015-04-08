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

class StatusesHomeTimelineCommand extends BaseCommand
{
    private $displayTable;
    private $table;
    
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
        
        $output->writeln('<comment>Number of tweets: '.$numberOfTweets.'</comment>');
        
        if ($numberOfTweets == 0)
        {
            $output->writeln('<comment>No new tweet.</comment>');
            return 0;
        }
        
        # Iterate through $content in order to add the oldest tweet first, 
        #  if there is an error the oldest tweet will still be saved
        #  and newer tweets can be saved next time the command is launched
        $tweets = array_reverse($content);
        
        $this->iterateTweets($input, $output, $tweets);
    }
    
    protected function setAndDisplayLastTweet($output)
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
    
    /**
     * @param InputInterface $input
     */
    protected function getContent($input)
    {
        if ($input->getOption('test'))
        {
            $content = json_decode(file_get_contents(
                dirname(__FILE__).'/../Tests/Command/data/tweets.json'));
        }
        else if ($input->getOption('notarray'))
        {
            $content = null;
        }
        else if ($input->getOption('emptyarray'))
        {
            $content = array();
        }
        else
        {
            $connection = new TwitterOAuth(
                $this->container->getParameter('twitter_consumer_key'),
                $this->container->getParameter('twitter_consumer_secret'),
                $this->container->getParameter('twitter_token'),
                $this->container->getParameter('twitter_token_secret')
            );
            
            $content = $connection->get(
                'statuses/home_timeline',
                $this->parameters
            );
        }
        
        return($content);
    }
    
    protected function displayContentNotArrayError($output, $content)
    {
        $formatter = $this->getHelper('formatter');
        
        $errorMessages = array('Error!', 'Something went wrong, $content is not an array.');
        $formattedBlock = $formatter->formatBlock($errorMessages, 'error');
        $output->writeln($formattedBlock);
        $output->writeln(print_r($content, true));
    }
    
    protected function iterateTweets($input, $output, $tweets)
    {
        $this->displayTable = $input->getOption('table');
        
        # Display
        if ($this->displayTable)
        {
            $this->table = $this->getHelper('table');
            $this->table
                ->setHeaders(array('Datetime', 'Text excerpt', 'Name'))
            ;
        }
        
        $progress = new ProgressBar($output, count($tweets));
        $progress->setBarCharacter('<comment>=</comment>');
        $progress->start();
        
        foreach ($tweets as $tweetTmp)
        {
            $this->addTweet($tweetTmp);
            
            $progress->advance();
        }
        
        $progress->finish();
        $output->writeln('');
        
        if ($this->displayTable)
        {
            $this->table->render($output);
        }
    }
    
    protected function persistUser($userTmp)
    {
        $user = $this->em
            ->getRepository('AsyncTweetsBundle:User')
            ->findOneById($userTmp->id)
        ;
        
        if (! $user)
        {
            $user = new User();
            
            # Only set the id when adding the user
            $user
                ->setId($userTmp->id)
            ;
        }
        
        # Update these fields
        $user
            ->setName($userTmp->name)
            ->setScreenName($userTmp->screen_name)
            ->setProfileImageUrl($userTmp->profile_image_url)
        ;
        
        $this->em->persist($user);
        
        return $user;
    }
    
    /**
     * @param stdClass Object $tweetTmp
     * @param User $user
     */
    protected function persistTweet($tweetTmp, $user)
    {
        $tweet = $this->em
            ->getRepository('AsyncTweetsBundle:Tweet')
            ->findOneById($tweetTmp->id)
        ;
        
        if (! $tweet)
        {
            $tweet = new Tweet();
            $tweet
                ->setId($tweetTmp->id)
                ->setCreatedAt(new \Datetime($tweetTmp->created_at))
                ->setText($tweetTmp->text)
                ->setRetweetCount($tweetTmp->retweet_count)
                ->setFavoriteCount($tweetTmp->favorite_count)
                ->setUser($user)
            ;
            
            if (
                (isset($tweetTmp->entities))
                &&
                (isset($tweetTmp->entities->media))
            )
            {
                foreach ($tweetTmp->entities->media as $mediaTmp)
                {
                    if ($mediaTmp->type == 'photo')
                    {
                        $this->persistMedia($tweet, $mediaTmp);
                    }
                }
            }
        }
        
        $this->em->persist($tweet);
        $this->em->flush();
        
        return $tweet;
    }
    
    /**
     * @param Tweet $tweet
     * @param stdClass Object $mediaTmp
     */
    protected function persistMedia($tweet, $mediaTmp)
    {
        $media = $this->em
            ->getRepository('AsyncTweetsBundle:Media')
            ->findOneById($mediaTmp->id)
        ;
        
        if (! $media)
        {
            $media = new Media();
            $media
                ->setId($mediaTmp->id)
            ;
        }
        
        $media
            ->setMediaUrlHttps($mediaTmp->media_url)
            ->setUrl($mediaTmp->url)
            ->setDisplayUrl($mediaTmp->display_url)
            ->setExpandedUrl($mediaTmp->expanded_url)
        ;
        
        $tweet->addMedia($media);
        
        $this->em->persist($media);
    }
    
    /**
     * @param stdClass Object $tweetTmp
     */
    protected function addTweet($tweetTmp)
    {
        $user = $this->persistUser($tweetTmp->user);
        
        $tweet = $this->persistTweet($tweetTmp, $user);
        
        if ($this->displayTable)
        {
            $this->table->addRow(array(
                $tweet->getCreatedAt()->format('Y-m-d H:i:s'),
                mb_substr($tweet->getText(), 0, 40),
                $user->getName()
            ));
        }
    }
}
