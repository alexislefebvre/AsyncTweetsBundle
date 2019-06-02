<?php

namespace Acme\Entity;

use AlexisLefebvre\Bundle\AsyncTweetsBundle\Entity\Tweet;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use Liip\TestFixturesBundle\Test\FixturesTrait;

class TweetRepositoryTest extends WebTestCase
{
    use FixturesTrait;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    public function setUp(): void
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        $this->em = static::$kernel->getContainer()->get('doctrine.orm.entity_manager');
    }

    public function testTweetRepository()
    {
        $this->loadFixtures([
            'Acme\DataFixtures\ORM\LoadTweetData',
        ]);

        $tweets = $this->em
            ->getRepository('AsyncTweetsBundle:Tweet')
            ->getWithUsers(1);

        $this->assertCount(3, $tweets);

        $tweets = $this->em
            ->getRepository('AsyncTweetsBundle:Tweet')
            ->getWithUsersAndMedias(null);

        $this->assertCount(3, $tweets);

        $tweets = $this->em
            ->getRepository('AsyncTweetsBundle:Tweet')
            ->getWithUsersAndMedias(null);

        $this->assertCount(3, $tweets);

        $tweets = $this->em
            ->getRepository('AsyncTweetsBundle:Tweet')
            ->countPendingTweets(49664);

        $this->assertSame(3, $tweets);
    }

    public function testTweetRepositoryWithLongTweet()
    {
        $this->loadFixtures([
            'Acme\DataFixtures\ORM\LoadTweetData',
        ]);

        /** @var Tweet $tweet */
        $tweet = $this->em
            ->getRepository('AsyncTweetsBundle:Tweet')
            ->findOneBy(['id' => 928032273747795968]);

        $this->assertNotNull($tweet);

        $this->assertSame(275, strlen($tweet->getText()));
    }
}
