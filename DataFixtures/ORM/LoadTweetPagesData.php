<?php

namespace AlexisLefebvre\Bundle\AsyncTweetsBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use AlexisLefebvre\Bundle\AsyncTweetsBundle\Entity\Tweet;

class LoadTweetPagesData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        foreach (range(1, 40) as $tweetId)
        {
            $tweet = new Tweet($tweetId);
            $tweet
                ->setUser($this->getReference('user'))
                ->setCreatedAt(new \Datetime('2015-02-10 21:19:'.$tweetId))
                ->setText($tweetId)
                ->setRetweetCount($tweetId)
                ->setFavoriteCount($tweetId)
                ->setInTimeline(true)
            ;
            
            $manager->persist($tweet);
        }
        
        $manager->flush();
        
        # Attach the same Media to 2 Tweet
        $tweet = $manager
            ->getRepository('AsyncTweetsBundle:Tweet')
            ->find(5);
        $tweet->addMedia($this->getReference('media'));
        
        $manager->persist($tweet);
        
        $tweet = $manager
            ->getRepository('AsyncTweetsBundle:Tweet')
            ->find(25);
        $tweet->addMedia($this->getReference('media'));
        
        $manager->persist($tweet);
        
        $manager->flush();
    }
    
    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 15; // the order in which fixtures will be loaded
    }
}
