<?php

namespace AlexisLefebvre\Bundle\AsyncTweetsBundle\Command;

use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BaseCommand extends ContainerAwareCommand
{
    protected $container;
    protected $em;

    protected function configure()
    {
        parent::configure();

        $this
            ->setName('statuses:base')
            ->setDescription('Base command');
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output); //initialize parent class method

        $this->container = $this->getContainer();

        // This loads Doctrine, you can load your own services as well
        /** @var ManagerRegistry $doctrine */
        $doctrine = $this->container->get('doctrine');
        $this->em = $doctrine->getManager();
    }
}
