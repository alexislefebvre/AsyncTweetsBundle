<?php

namespace AlexisLefebvre\Bundle\AsyncTweetsBundle\DataFixtures\ORM;

use AlexisLefebvre\Bundle\AsyncTweetsBundle\Entity\Tweet;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadTweetData extends AbstractFixture implements DependentFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $tweet = new Tweet(49664);
        $tweet
            ->setUser($this->getReference('user'))
            ->setCreatedAt(new \Datetime('2015-02-10 21:19:20'))
            ->setText('Hello Twitter! #myfirstTweet')
            ->setRetweetCount(0)
            ->setFavoriteCount(0)
            ->setInTimeline(true);

        $manager->persist($tweet);
        $manager->flush();

        $tweet = new Tweet();
        $tweet
            ->setId(210900500)
            ->setUser($this->getReference('user'))
            ->setCreatedAt(new \Datetime('2015-02-18 00:01:14'))
            ->setText('#image #test http://t.co/rX1oieH1ug')
            ->setRetweetCount(0)
            ->setFavoriteCount(0)
            ->setInTimeline(true)
            ->addMedia($this->getReference('media-1'));

        $manager->persist($tweet);
        $manager->flush();

        // Tweet with retweet
        $tweet = new Tweet();
        $tweet
            ->setId(240926208)
            ->setUser($this->getReference('user-github'))
            ->setCreatedAt(new \Datetime('2015-08-20 17:00:27'))
            ->setText('RT @GitHubEng: Cross-platform UI in GitHub '.
                'Desktop by @rob_rix http://t.co/j1SautZKs7')
            ->setRetweetCount(77)
            ->setFavoriteCount(0)
            ->setInTimeline(true);

        $retweet = new Tweet();
        $retweet
            ->setId(1005868289)
            ->setUser($this->getReference('user-githubeng'))
            ->setCreatedAt(new \Datetime('2015-08-19 01:10:01'))
            ->setText('Cross-platform UI in GitHub Desktop by @rob_rix '.
                'http://t.co/j1SautZKs7')
            ->setRetweetCount(77)
            ->setFavoriteCount(151);

        $manager->persist($retweet);
        $manager->flush();

        $tweet->setRetweetedStatus($retweet);

        $manager->persist($tweet);
        $manager->flush();

        $tweet = new Tweet();
        $tweet
            ->setId(1005868490)
            ->setUser($this->getReference('user-githubeng'))
            ->setCreatedAt(new \Datetime('2015-08-25 04:10:01'))
            ->setText('Cross-platform UI in GitHub Desktop by @rob_rix '.
                'http://t.co/j1SautZKs7')
            ->setRetweetCount(7)
            ->setFavoriteCount(42)
            ->addMedia($this->getReference('media-2'));

        $manager->persist($tweet);
        $manager->flush();

        // 280 characters
        $tweet = new Tweet();
        $tweet
            ->setId(928032273747795968)
            ->setUser($this->getReference('user'))
            ->setCreatedAt(new \Datetime('2017-11-08 21:20:00'))
            ->setText('In the criminal justice system, sexually based offenses are considered especially heinous. '.
                'In New York City, the dedicated detectives who investigate these vicious felonies are members of '.
                'an elite squad known as the Special Victims Unit. These are their stories. *DUN DUN*️')
            ->setRetweetCount(144416)
            ->setFavoriteCount(256453);

        $manager->persist($tweet);
        $manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getDependencies()
    {
        return [
            'AlexisLefebvre\Bundle\AsyncTweetsBundle\DataFixtures\ORM\LoadMediaData',
        ];
    }
}
