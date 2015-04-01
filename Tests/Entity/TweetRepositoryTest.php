<?php

namespace AsyncTweets\AsyncTweetsBundle\Tests\Entity;

use Liip\FunctionalTestBundle\Test\WebTestCase;

use AsyncTweets\AsyncTweetsBundle\Entity\Media;
use AsyncTweets\AsyncTweetsBundle\Entity\Tweet;
use AsyncTweets\AsyncTweetsBundle\Entity\User;

class TweetRepositoryTest extends WebTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    public function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        $this->em = static::$kernel->getContainer()->get('doctrine.orm.entity_manager');
    }
    
    public function testTweetRepository()
    {
        $this->loadFixtures(array(
            'AsyncTweetsBundle\DataFixtures\ORM\LoadUserData',
            'AsyncTweetsBundle\DataFixtures\ORM\LoadTweetData',
            'AsyncTweetsBundle\DataFixtures\ORM\LoadMediaData',
        ));
        
        $tweets = $this->em
            ->getRepository('AsyncTweetsBundle:Tweet')
            ->getWithUsers(1)
        ;

        $this->assertCount(1, $tweets);
        
        $tweets = $this->em
            ->getRepository('AsyncTweetsBundle:Tweet')
            ->getWithUsersAndMedias(null, false)
        ;

        $this->assertCount(1, $tweets);
        
        $tweets = $this->em
            ->getRepository('AsyncTweetsBundle:Tweet')
            ->getWithUsersAndMedias(null, true)
        ;

        $this->assertCount(1, $tweets);
        
        $tweets = $this->em
            ->getRepository('AsyncTweetsBundle:Tweet')
            ->countPendingTweets(565258739000049664)
        ;
        
        $this->assertEquals(1, $tweets);
    }
}
