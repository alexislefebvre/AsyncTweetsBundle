<?php

namespace AlexisLefebvre\Bundle\AsyncTweetsBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Abraham\TwitterOAuth\TwitterOAuth;

class StatusesShowCommand extends BaseCommand
{
   protected function configure()
    {
        parent::configure();
        
        $this
            ->setName('statuses:show')
            ->setDescription('Show one tweet (for debugging)')
            ->addArgument('tweet_id', InputArgument::REQUIRED, 'Tweet ID')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $tweet_id = $input->getArgument('tweet_id');
        
        $connection = new TwitterOAuth(
            $this->container->getParameter('twitter_consumer_key'),
            $this->container->getParameter('twitter_consumer_secret'),
            $this->container->getParameter('twitter_token'),
            $this->container->getParameter('twitter_token_secret')
        );
        
        echo(json_encode($connection->get('statuses/show/'.$tweet_id)));
    }
}
