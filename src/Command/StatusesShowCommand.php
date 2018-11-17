<?php

namespace AlexisLefebvre\Bundle\AsyncTweetsBundle\Command;

use Abraham\TwitterOAuth\TwitterOAuth;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StatusesShowCommand extends BaseCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('statuses:show')
            ->setDescription('Show one tweet (for debugging)')
            ->addArgument('tweet_id', InputArgument::REQUIRED, 'Tweet ID');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var int|null $tweet_id */
        $tweet_id = $input->getArgument('tweet_id');

        $connection = new TwitterOAuth(
            $this->container->getParameter('twitter_consumer_key'),
            $this->container->getParameter('twitter_consumer_secret'),
            $this->container->getParameter('twitter_token'),
            $this->container->getParameter('twitter_token_secret')
        );

        /** @var string $json */
        $json = json_encode($connection->get(sprintf(
            'statuses/show/%d',
            $tweet_id
        )));

        $output->writeln($json);
    }
}
