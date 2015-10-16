<?php

namespace AlexisLefebvre\Bundle\AsyncTweetsBundle\Tests\Entity;

use Liip\FunctionalTestBundle\Test\WebTestCase;

use AlexisLefebvre\Bundle\AsyncTweetsBundle\Entity\Media;
use AlexisLefebvre\Bundle\AsyncTweetsBundle\Entity\Tweet;
use AlexisLefebvre\Bundle\AsyncTweetsBundle\Entity\User;

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
            'AlexisLefebvre\Bundle\AsyncTweetsBundle\DataFixtures\ORM\LoadUserData',
            'AlexisLefebvre\Bundle\AsyncTweetsBundle\DataFixtures\ORM\LoadTweetData',
            'AlexisLefebvre\Bundle\AsyncTweetsBundle\DataFixtures\ORM\LoadMediaData',
        ));
        
        $tweets = $this->em
            ->getRepository('AsyncTweetsBundle:Tweet')
            ->getWithUsers(1)
        ;

        $this->assertCount(3, $tweets);
        
        $tweets = $this->em
            ->getRepository('AsyncTweetsBundle:Tweet')
            ->getWithUsersAndMedias(null, false)
        ;

        $this->assertCount(3, $tweets);
        
        $tweets = $this->em
            ->getRepository('AsyncTweetsBundle:Tweet')
            ->getWithUsersAndMedias(null, true)
        ;

        $this->assertCount(3, $tweets);
        
        $tweets = $this->em
            ->getRepository('AsyncTweetsBundle:Tweet')
            ->countPendingTweets((PHP_INT_SIZE === 8) ? 565258739000049664 : 49664)
        ;
        
        $this->assertEquals(3, $tweets);
    }
}
