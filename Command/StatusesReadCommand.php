<?php

namespace AlexisLefebvre\Bundle\AsyncTweetsBundle\Command;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StatusesReadCommand extends BaseCommand
{
    private $table;

    protected function configure()
    {
        parent::configure();

        $this
            ->setName('statuses:read')
            ->setDescription('Read home timeline')
            ->addArgument('page', InputArgument::OPTIONAL, 'Page');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $page = $input->getArgument('page');

        if ($page < 1) {
            $page = 1;
        }

        $output->writeln('Current page: <comment>'.$page.'</comment>');

        // Get the tweets
        $tweets = $this->em
            ->getRepository('AsyncTweetsBundle:Tweet')
            ->getWithUsers($page);

        if (!$tweets) {
            $output->writeln('<info>No tweet to display.</info>');

            return 0;
        }

        $this->displayTweets($output, $tweets);
    }

    /**
     * @param OutputInterface $output
     * @param array           $tweets
     */
    protected function displayTweets(OutputInterface $output,
        $tweets)
    {
        $this->setTable($output);

        foreach ($tweets as $tweet) {
            $this->table->addRows([
                [
                    $this->formatCell('info',
                        $tweet->getUser()->getName(), 13),
                    $this->formatCell('comment',
                        $tweet->getText(), 40),
                    $tweet->getCreatedAt()->format('Y-m-d H:i'),
                ],
                // empty row between tweets
                ['', '', ''], ]
            );
        }

        $this->table->render();
    }

    protected function setTable(OutputInterface $output)
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

    /**
     * @param string $tag
     * @param string $content
     * @param int    $length
     * 
     * @return string
     */
    protected function formatCell($tag, $content, $length)
    {
        return '<'.$tag.'>'.
            // Close and reopen the tag before each new line
            str_replace("\n", '</'.$tag.">\n<".$tag.'>',
                wordwrap($content, $length, "\n")
            ).
            '</'.$tag.'>';
    }
}
