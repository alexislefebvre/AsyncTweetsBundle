<?php

namespace Acme\DataFixtures\ORM;

use AlexisLefebvre\Bundle\AsyncTweetsBundle\Entity\Tweet;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class LoadTweetPagesData extends AbstractFixture implements DependentFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $tweetId = 1;

        // Add retweeted Tweet
        $retweeted = new Tweet();
        $retweeted
            ->setId($tweetId)
            ->setUser($this->getReference('user-githubeng'))
            ->setCreatedAt(new \Datetime(
                '2015-02-10 21:18:'.sprintf('%02d', $tweetId)
            ))
            ->setText($tweetId)
            ->setRetweetCount($tweetId)
            ->setFavoriteCount($tweetId);

        $manager->persist($retweeted);

        // not retweet tweets
        foreach (range(2, 40) as $tweetId) {
            $tweet = new Tweet();
            $tweet
                ->setId($tweetId)
                ->setUser($this->getReference('user'))
                ->setCreatedAt(
                    new \Datetime('2015-02-10 21:19:'.sprintf('%02d', $tweetId))
                )
                ->setText($tweetId)
                ->setRetweetCount($tweetId)
                ->setFavoriteCount($tweetId)
                ->setInTimeline(true);

            $manager->persist($tweet);
        }

        $manager->flush();

        // Attach the same Media to 2 Tweet
        $tweet = $manager
            ->getRepository(Tweet::class)
            ->find(5);
        $tweet->addMedia($this->getReference('media-1'));

        $manager->persist($tweet);

        $tweet = $manager
            ->getRepository(Tweet::class)
            ->find(25);
        $tweet->addMedia($this->getReference('media-2'));

        $manager->persist($tweet);

        $manager->flush();

        // Set tweet as retweet
        $retweet = $manager
            ->getRepository(Tweet::class)
            ->find(15);

        $retweet->setRetweetedStatus($retweeted);

        $manager->persist($tweet);
    }

    /**
     * {@inheritdoc}
     */
    public function getDependencies()
    {
        return ['Acme\DataFixtures\ORM\LoadMediaData'];
    }
}
