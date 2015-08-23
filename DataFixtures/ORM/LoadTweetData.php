<?php

namespace AlexisLefebvre\Bundle\AsyncTweetsBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use AlexisLefebvre\Bundle\AsyncTweetsBundle\Entity\Tweet;

class LoadTweetData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $tweet = new Tweet(565258739000049664);
        $tweet
            ->setUser($this->getReference('user'))
            ->setCreatedAt(new \Datetime('2015-02-10 21:19:20'))
            ->setText('Hello Twitter! #myfirstTweet')
            ->setRetweetCount(0)
            ->setFavoriteCount(0)
            ->setInTimeline(true)
        ;
        
        $manager->persist($tweet);
        $manager->flush();
        
        $tweet = new Tweet();
        $tweet
            ->setId(567836201210900500)
            ->setUser($this->getReference('user'))
            ->setCreatedAt(new \Datetime('2015-02-18 00:01:14'))
            ->setText('#image #test http://t.co/rX1oieH1ug')
            ->setRetweetCount(0)
            ->setFavoriteCount(0)
            ->setInTimeline(true)
            ->addMedia($this->getReference('media'))
        ;
        
        $manager->persist($tweet);
        $manager->flush();
        
        # Tweet with retweet
        $tweet = new Tweet();
        $tweet
            ->setId(634047285240926208)
            ->setUser($this->getReference('user-github'))
            ->setCreatedAt(new \Datetime('2015-08-19 17:00:27'))
            ->setText('RT @GitHubEng: Cross-platform UI in GitHub '.
                'Desktop by @rob_rix http://t.co/j1SautZKs7')
            ->setRetweetCount(77)
            ->setFavoriteCount(0)
            ->setInTimeline(true)
        ;
        
        $retweet = new Tweet();
        $retweet
            ->setId(634046200505868289)
            ->setUser($this->getReference('user-githubeng'))
            ->setCreatedAt(new \Datetime('2015-08-20 01:10:01'))
            ->setText('Cross-platform UI in GitHub Desktop by @rob_rix '.
                'http://t.co/j1SautZKs7')
            ->setRetweetCount(77)
            ->setFavoriteCount(151)
        ;
        
        $manager->persist($retweet);
        $manager->flush();
        
        $tweet->setRetweetedStatus($retweet);
        
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
