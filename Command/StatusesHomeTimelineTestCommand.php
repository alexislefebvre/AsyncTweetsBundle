<?php

namespace AlexisLefebvre\Bundle\AsyncTweetsBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;

class StatusesHomeTimelineTestCommand extends StatusesHomeTimelineCommand
{
    protected function configure()
    {
        parent::configure();
        
        $this
            ->setName('statuses:hometimelinetest')
            ->setDescription('Fetch home timeline test')
            ->addArgument(
                'test',
                InputArgument::OPTIONAL,
                'Return data for tests'
            )    
        ;
    }
    
    /**
     * Read tweet(s) from a JSON file
     * 
     * @param string $filename
     * @return array
     */
    protected function getTestContent($filename)
    {
        /** @see https://insight.sensiolabs.com/what-we-analyse/symfony.dependency_injection.use_dir_file_constant */
        return(json_decode(file_get_contents(
            $this->container->get('kernel')->locateResource(
                '@AsyncTweetsBundle/Tests/Command/data/'.$filename
            )
        )));
    }
    
    /**
     * @param InputInterface $input
     */
    protected function getContent(InputInterface $input)
    {
        switch($input->getArgument('test')) {
            case 'json':
                return($this->getTestContent(
                    'tweets_32_bits.json'));
            case 'json_with_retweet':
                return($this->getTestContent(
                    'tweet_with_retweet.json'));
            case 'not_array':
                // Return null instead of JSON
                return(null);
            case 'empty_array':
                // Return an empty array instead of JSON
                return(array());
            default:
                // Normal behaviour
                return(parent::getContent($input)); 
        }
    }
}
