<?php

namespace Acme\App;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

/**
 * @see http://www.whitewashing.de/2012/02/25/symfony2_controller_testing.html
 */
class AppKernel extends Kernel
{
    /**
     * @see https://stackoverflow.com/questions/20743060/symfony2-and-date-default-timezone-get-it-is-not-safe-to-rely-on-the-system/20743237#20743237
     */
    public function __construct(string $environment, bool $debug)
    {
        date_default_timezone_set('UTC');

        parent::__construct($environment, $debug);
    }

    public function registerBundles()
    {
        $bundles = [
            new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new \Symfony\Bundle\TwigBundle\TwigBundle(),
            new \Symfony\Bundle\MonologBundle\MonologBundle(),
            new \Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new \AlexisLefebvre\Bundle\AsyncTweetsBundle\AsyncTweetsBundle(),
            new \Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle(),
            new \Liip\FunctionalTestBundle\LiipFunctionalTestBundle(),
            new \Liip\TestFixturesBundle\LiipTestFixturesBundle(),
            new \FriendsOfBehat\SymfonyExtension\Bundle\FriendsOfBehatSymfonyExtensionBundle(),
        ];

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config.yml');
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheDir()
    {
        return $this->getProjectDir().'/tests/App/var/cache/'.$this->environment;
    }

    /**
     * {@inheritdoc}
     */
    public function getLogDir()
    {
        return $this->getProjectDir().'/tests/App/var/log';
    }
}
