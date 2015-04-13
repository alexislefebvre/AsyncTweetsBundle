<?php

namespace AlexisLefebvre\Bundle\AsyncTweetsBundle\Command;

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
            ->addArgument('page', InputArgument::OPTIONAL, 'Page')
        ;
    }
    
    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $page = $input->getArgument('page');
        
        if ($page < 1) {$page = 1;}
        
        $output->writeln('Current page: <comment>'.$page.'</comment>');
        
        # Get the tweets
        $tweets = $this->em
            ->getRepository('AsyncTweetsBundle:Tweet')
            ->getWithUsers($page);
        
        if (! $tweets)
        {
            $output->writeln('<info>No tweet to display.</info>');
            
            return 0;
        }
        
        $this->displayTweets($output, $tweets);
    }
    
    /**
     * @param OutputInterface $output
     * @param array $tweets
     */
    protected function displayTweets(OutputInterface $output,
        $tweets)
    {
        $this->setTable();
        
        foreach ($tweets as $tweet)
        {
            $this->table->addRows(array(
                array(
                    '<info>'.
                        # Close and reopen the tag before each new line
                        str_replace("\n", "</info>\n<info>",
                            wordwrap($tweet->getUser()->getName(), 13, "\n")
                        ).
                    '</info>',
                    
                    '<comment>'.
                        # Close and reopen the tag before each new line
                        str_replace("\n", "</comment>\n<comment>",
                            wordwrap($tweet->getText(), 40, "\n")
                        ).
                    '</comment>',
                    
                    $tweet->getCreatedAt()->format('Y-m-d H:i'),
                ),
                # empty row
                array('', '', ''))
            );
        }
        
        $this->displayTable($output);
    }
    
    protected function setTable()
    {
        $this->table = $this->getHelper('table');
        $this->table
            ->setHeaders(array(
                # Add spaces to use all the 80 columns,
                #  even if name or texts are short
                sprintf('%-13s', 'Name'),
                sprintf('%-40s', 'Text'),
                sprintf('%-16s', 'Datetime'),
            ))
        ;
    }
    
    /**
     * @param OutputInterface $output
     */
    protected function displayTable(OutputInterface $output)
    {
        $this->table->render($output);
    }
}
