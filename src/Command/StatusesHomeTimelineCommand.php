<?php

namespace AlexisLefebvre\Bundle\AsyncTweetsBundle\Command;

use Abraham\TwitterOAuth\TwitterOAuth;
use AlexisLefebvre\Bundle\AsyncTweetsBundle\Entity\Tweet;
use AlexisLefebvre\Bundle\AsyncTweetsBundle\Entity\TweetRepository;
use AlexisLefebvre\Bundle\AsyncTweetsBundle\Utils\PersistTweet;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class StatusesHomeTimelineCommand extends BaseCommand
{
    /** @var bool */
    private $displayTable;
    /** @var Table */
    private $table;
    /** @var ProgressBar */
    private $progress;

    /**
     * @var array<bool|int>
     *
     * @see https://dev.twitter.com/rest/reference/get/statuses/home_timeline
     */
    private $parameters = [
        'count'           => 200,
        'exclude_replies' => true,
    ];

    protected function configure(): void
    {
        parent::configure();

        $this
            ->setName('statuses:hometimeline')
            ->setDescription('Fetch home timeline')
            // http://symfony.com/doc/2.3/cookbook/console/console_command.html#automatically-registering-commands
            ->addOption(
                'table',
                null,
                InputOption::VALUE_NONE,
                'Display a table with tweets'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->setAndDisplayLastTweet($output);

        $content = $this->getContent($input);

        if (!is_array($content)) {
            $this->displayContentNotArrayError($output, $content);

            return 1;
        }

        $numberOfTweets = count($content);

        if ($numberOfTweets == 0) {
            $output->writeln('<comment>No new tweet.</comment>');

            return 0;
        }

        $this->addAndDisplayTweets($input, $output, $content, $numberOfTweets);

        return 0;
    }

    protected function setAndDisplayLastTweet(OutputInterface $output): void
    {
        /** @var TweetRepository $tweetRepository */
        $tweetRepository = $this->em
            ->getRepository(Tweet::class);

        // Get the last tweet
        $lastTweet = $tweetRepository->getLastTweet();

        // And use it in the request if it exists
        if ($lastTweet) {
            $this->parameters['since_id'] = $lastTweet->getId();

            $comment = 'last tweet = '.$this->parameters['since_id'];
        } else {
            $comment = 'no last tweet';
        }

        $output->writeln('<comment>'.$comment.'</comment>');
    }

    /**
     * @return array<\stdClass>|object
     */
    protected function getContent(InputInterface $input)
    {
        $connection = new TwitterOAuth(
            (string) $this->container->getParameter('twitter_consumer_key'),
            (string) $this->container->getParameter('twitter_consumer_secret'),
            (string) $this->container->getParameter('twitter_token'),
            (string) $this->container->getParameter('twitter_token_secret')
        );

        return $connection->get(
            'statuses/home_timeline',
            $this->parameters
        );
    }

    /**
     * @param array<string>|object $content
     */
    protected function displayContentNotArrayError(
        OutputInterface $output,
        $content
    ): void {
        $formatter = $this->getHelper('formatter');

        $errorMessages = ['Error!', 'Something went wrong, $content is not an array.'];
        $formattedBlock = $formatter->formatBlock($errorMessages, 'error');
        $output->writeln($formattedBlock);
        $output->writeln(print_r($content, true));
    }

    /**
     * @param array<\stdClass> $content
     */
    protected function addAndDisplayTweets(
        InputInterface $input,
        OutputInterface $output,
        array $content,
        int $numberOfTweets
    ): void {
        $output->writeln('<comment>Number of tweets: '.$numberOfTweets.'</comment>');

        // Iterate through $content in order to add the oldest tweet first,
        //  if there is an error the oldest tweet will still be saved
        //  and newer tweets can be saved next time the command is launched
        $tweets = array_reverse($content);

        $this->setProgressBar($output, $numberOfTweets);
        $this->setTable($input, $output);
        $this->iterateTweets($tweets);

        $this->progress->finish();
        $output->writeln('');

        if ($this->displayTable) {
            $this->table->render();
        }
    }

    protected function setProgressBar(
        OutputInterface $output,
        int $numberOfTweets
    ): void {
        $this->progress = new ProgressBar($output, $numberOfTweets);
        $this->progress->setBarCharacter('<comment>=</comment>');
        $this->progress->start();
    }

    protected function setTable(
        InputInterface $input,
        OutputInterface $output
    ): void {
        $this->displayTable = (bool) $input->getOption('table');

        // Display
        if ($this->displayTable) {
            $this->table = new Table($output);
            $this->table
                ->setHeaders(['Datetime', 'Text excerpt', 'Name']);
        }
    }

    /**
     * @param array<\stdClass> $tweets
     */
    protected function iterateTweets(array $tweets): void
    {
        $persistTweet = new PersistTweet(
            $this->em,
            $this->displayTable,
            $this->table
        );

        foreach ($tweets as $tweetTmp) {
            $persistTweet->addTweet($tweetTmp, true);

            $this->progress->advance();
        }
    }
}
