<?php

namespace Acme\Command;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;

/**
 * @see http://symfony.com/doc/current/cookbook/console/console_command.html#testing-commands
 */
class StatusesBase extends WebTestCase
{
    public $application;

    public function setUp(): void
    {
        $kernel = $this->createKernel();
        $kernel->boot();

        $this->application = new Application($kernel);
    }
}
