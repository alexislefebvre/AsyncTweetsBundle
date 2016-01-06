<?php

namespace AlexisLefebvre\Bundle\AsyncTweetsBundle\DataFixtures\ORM;

use AlexisLefebvre\Bundle\AsyncTweetsBundle\Entity\Tweet;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadTweetAndRetweetData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $tweet = new Tweet(10);
        $tweet
            ->setUser($this->getReference('user'))
            ->setCreatedAt(new \Datetime('2015-02-10 21:19:20'))
            ->setText('Hello Twitter! #myfirstTweet')
            ->setRetweetCount(0)
            ->setFavoriteCount(0)
            ->setInTimeline(true);

        $manager->persist($tweet);
        $manager->flush();

        // Retweeted Tweet
        $retweet = new Tweet();
        $retweet
            ->setId(20)
            ->setUser($this->getReference('user-githubeng'))
            ->setCreatedAt(new \Datetime('2015-08-19 01:10:01'))
            ->setText('Cross-platform UI in GitHub')
            ->setInTimeline(true);

        $manager->persist($retweet);
        $manager->flush();

        // Tweet with retweet
        $tweet = new Tweet();
        $tweet
            ->setId(30)
            ->setUser($this->getReference('user-github'))
            ->setCreatedAt(new \Datetime('2015-08-20 17:00:27'))
            ->setText('RT @GitHubEng: Cross-platform UI in GitHub')
            ->setInTimeline(true);

        $tweet->setRetweetedStatus($retweet);

        $manager->persist($tweet);
        $manager->flush();

        // Tweet
        $tweet = new Tweet();
        $tweet
            ->setId(40)
            ->setUser($this->getReference('user-githubeng'))
            ->setCreatedAt(new \Datetime('2015-08-25 04:10:01'))
            ->setText('Next tweet')
            ->setInTimeline(true);

        $manager->persist($tweet);
        $manager->flush();

        // Tweet
        $tweet = new Tweet();
        $tweet
            ->setId(50)
            ->setUser($this->getReference('user-githubeng'))
            ->setCreatedAt(new \Datetime('2015-08-28 14:11:01'))
            ->setText('Last tweet')
            ->setInTimeline(true);

        $manager->persist($tweet);
        $manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 15; // the order in which fixtures will be loaded
    }
}
