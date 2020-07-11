<?php

namespace AlexisLefebvre\Bundle\AsyncTweetsBundle\Command;

use AlexisLefebvre\Bundle\AsyncTweetsBundle\Entity\Tweet;
use AlexisLefebvre\Bundle\AsyncTweetsBundle\Entity\TweetRepository;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StatusesReadCommand extends BaseCommand
{
    /** @var Table */
    private $table;

    protected function configure(): void
    {
        parent::configure();

        $this
            ->setName('statuses:read')
            ->setDescription('Read home timeline')
            ->addArgument('page', InputArgument::OPTIONAL, 'Page');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var int $page */
        $page = $input->getArgument('page');

        if ($page < 1) {
            $page = 1;
        }

        $output->writeln(sprintf(
            'Current page: <comment>%d</comment>',
            $page
        ));

        /** @var TweetRepository $tweetRepository */
        $tweetRepository = $this->em
            ->getRepository(Tweet::class);
        // Get the tweets
        $tweets = $tweetRepository->getWithUsers($page);

        if (!$tweets) {
            $output->writeln('<info>No tweet to display.</info>');

            return 0;
        }

        $this->displayTweets($output, $tweets);

        return 0;
    }

    /**
     * @param array<Tweet> $tweets
     */
    protected function displayTweets(
        OutputInterface $output,
        array $tweets
    ): void {
        $this->setTable($output);

        foreach ($tweets as $tweet) {
            $this->table->addRows(
                [
                    [
                        $this->formatCell(
                            'info',
                            $tweet->getUser()->getName(),
                            13
                        ),
                        $this->formatCell(
                            'comment',
                            $tweet->getText(),
                            40
                        ),
                        $tweet->getCreatedAt()->format('Y-m-d H:i'),
                    ],
                    // empty row between tweets
                    ['', '', ''],
                ]
            );
        }

        $this->table->render();
    }

    protected function setTable(OutputInterface $output): void
    {
        $this->table = new Table($output);
        $this->table
            ->setHeaders([
                // Add spaces to use all the 80 columns,
                //  even if name or texts are short
                sprintf('%-13s', 'Name'),
                sprintf('%-40s', 'Text'),
                sprintf('%-16s', 'Datetime'),
            ]);
    }

    protected function formatCell(string $tag, string $content, int $length): string
    {
        return '<'.$tag.'>'.
            // Close and reopen the tag before each new line
            str_replace(
                "\n",
                '</'.$tag.">\n<".$tag.'>',
                wordwrap($content, $length, "\n")
            ).
            '</'.$tag.'>';
    }
}
