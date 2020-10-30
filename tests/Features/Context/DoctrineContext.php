<?php

namespace Acme\Features\Context;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\Persistence\ObjectManager;
use Nelmio\Alice\Loader\NativeLoader;
use Symfony\Bridge\Doctrine\ManagerRegistry;

class DoctrineContext implements Context
{
    /**
     * @var ObjectManager
     */
    private $manager;

    /**
     * @var SchemaTool
     */
    private $schemaTool;

    /**
     * @var array
     */
    private $classes;

    /**
     * @var \Nelmio\Alice\Loader\NativeLoader
     */
    private $nativeLoader;

    /**
     * @var \Nelmio\Alice\ObjectSet
     */
    private $objectSet;

    public function __construct(
        ManagerRegistry $doctrine
    ) {
        $this->manager = $doctrine->getManager();
        $this->schemaTool = new SchemaTool($this->manager);
        $this->classes = $this->manager->getMetadataFactory()->getAllMetadata();
    }

    /**
     * @BeforeScenario
     */
    public function dropData()
    {
        $purger = new ORMPurger();
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_DELETE);

        $executor = new ORMExecutor($this->manager, $purger);
        $executor->execute([]);
    }

    /**
     * @AfterScenario @dropSchema
     */
    public function dropDatabase()
    {
        $this->schemaTool->dropSchema($this->classes);
    }

    /**
     * @When I load the following fixtures:
     */
    public function loadFixtures(TableNode $data)
    {
        $this->nativeLoader = new NativeLoader();

        $files = [];

        foreach ($data->getRows() as $row) {
            $name = $row[0];
            $filePath = sprintf('%s/../../Fixtures/%s.yml', __DIR__, $name);

            if (!file_exists($filePath)) {
                throw new \RuntimeException(sprintf('File %s not found', $name));
            }

            $files[] = $filePath;
        }

        $this->objectSet = $this->nativeLoader->loadFiles($files)->getObjects();

        foreach ($this->objectSet as $object) {
            $this->manager->persist($object);
        }

        $this->manager->flush();
    }
}
