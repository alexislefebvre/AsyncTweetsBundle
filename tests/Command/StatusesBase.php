<?php

namespace Acme\Command;

use AlexisLefebvre\TestBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;

/**
 * @see http://symfony.com/doc/current/cookbook/console/console_command.html#testing-commands
 */
class StatusesBase extends WebTestCase
{
    public $application;

    public function setUp()
    {
        $kernel = $this->createKernel();
        $kernel->boot();

        $this->application = new Application($kernel);
    }
}
