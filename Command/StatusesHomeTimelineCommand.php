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
        // @codeCoverageIgnoreStart
        if (! $input->getOption('test'))
        {
            $connection = new TwitterOAuth(
                $this->container->getParameter('twitter_consumer_key'),
                $this->container->getParameter('twitter_consumer_secret'),
                $this->container->getParameter('twitter_token'),
                $this->container->getParameter('twitter_token_secret')
            );
        }
        // @codeCoverageIgnoreEnd
        
        /** @see https://dev.twitter.com/rest/reference/get/statuses/home_timeline */
        $parameters = array(
            'count' => 200,
            'exclude_replies' => true
        );
        
        # Get the last tweet
        $lastTweet = $this->em
            ->getRepository('AsyncTweetsBundle:Tweet')
            /** @see http://doctrine-orm.readthedocs.org/en/latest/reference/working-with-objects.html#by-simple-conditions */
            ->findOneBy(
                # Conditions
                array(),
                # Orderings
                array('id' => 'DESC'),
                # Limit
                1,
                # Offset
                0
            )
        ;
        
        # And use it in the request if it exists
        if ($lastTweet)
        {
            $parameters['since_id'] = $lastTweet->getId();
            
            $comment = 'since_id parameter = '.$parameters['since_id'];
        }
        else
        {
            $comment = 'no since_id parameter';
        }
        
        $output->writeln('<comment>'.$comment.'</comment>');
        
        if ($input->getOption('test'))
        {
            $content = json_decode(file_get_contents(
                dirname(__FILE__).'/../Tests/data/tweets.json'));
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
            // @codeCoverageIgnoreStart
            $content = $connection->get('statuses/home_timeline', $parameters);
            // @codeCoverageIgnoreEnd
        }
        
        if (! is_array($content))
        {
            $formatter = $this->getHelper('formatter');
            
            $errorMessages = array('Error!', 'Something went wrong, $content is not an array.');
            $formattedBlock = $formatter->formatBlock($errorMessages, 'error');
            $output->writeln($formattedBlock);
            $output->writeln(print_r($content, true));
            return 1;
        }
        
        $numberOfTweets = count($content);
        
        $output->writeln('<comment>Number of tweets: '.$numberOfTweets.'</comment>');
        
        if ($numberOfTweets == 0)
        {
            $output->writeln('<comment>No new tweet.</comment>');
            return 0;
        }
        
        $this->displayTable = $input->getOption('table');
        
        # Display
        if ($this->displayTable)
        {
            $this->table = $this->getHelper('table');
            $this->table
                ->setHeaders(array('Datetime', 'Text excerpt', 'Name'))
            ;
        }
        
        # Iterate through $content in order to add the oldest tweet first, 
        #  if there is an error the oldest tweet will still be saved
        #  and newer tweets can be saved next time
        $content = array_reverse($content);
        
        $progress = new ProgressBar($output, $numberOfTweets);
        $progress->setBarCharacter('<comment>=</comment>');
        $progress->start();
        
        foreach ($content as $tweetTmp)
        {
            $this->persistTweet($tweetTmp);
            
            $progress->advance();
        }
        
        $progress->finish();
        $output->writeln('');
        
        if ($this->displayTable)
        {
            $this->table->render($output);
        }
    }
    
    protected function persistTweet($tweetTmp)
    {
        $userTmp = $tweetTmp->user;
        
        # User
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
        
        # Tweet
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
                // @codeCoverageIgnoreStart
                (isset($tweetTmp->entities->media))
                // @codeCoverageIgnoreEnd
            )
            {
                foreach ($tweetTmp->entities->media as $mediaTmp)
                {
                    // @codeCoverageIgnoreStart
                    if ($mediaTmp->type !== 'photo')
                    {
                        continue;
                    }
                    // @codeCoverageIgnoreEnd
                    
                    # Media
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
            }
        }
        
        $this->em->persist($tweet);
        $this->em->flush();
        
        if ($this->displayTable)
        {
            $this->table->addRow(array(
                $tweetTmp->created_at,
                mb_substr($tweetTmp->text, 0, 20),
                $userTmp->name
            ));
        }
    }
}
