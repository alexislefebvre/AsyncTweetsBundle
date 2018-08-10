<?php

namespace Acme\Command;

use AlexisLefebvre\Bundle\AsyncTweetsBundle\Command\StatusesHomeTimelineCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;

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
            );
    }

    /**
     * Read tweet(s) from a JSON file.
     *
     * @param string $filename
     *
     * @return array
     */
    protected function getTestContent($filename)
    {
        return json_decode(file_get_contents(
            sprintf(__DIR__.'/../../tests/Command/data/%s', $filename)
        ));
    }

    /**
     * @param InputInterface $input
     *
     * @return string|null|array
     */
    protected function getContent(InputInterface $input)
    {
        switch ($input->getArgument('test')) {
            case 'json':
                return $this->getTestContent(
                    'tweets_32_bits.json');
            case 'json_with_retweet':
                return $this->getTestContent(
                    'tweet_with_retweet.json');
            case 'not_array':
                // Return null instead of JSON
                return;
            case 'empty_array':
                // Return an empty array instead of JSON
                return [];
            default:
                // Normal behaviour
                return parent::getContent($input);
        }
    }
}
