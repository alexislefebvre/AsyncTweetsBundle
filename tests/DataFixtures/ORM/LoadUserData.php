<?php

namespace Acme\DataFixtures\ORM;

use AlexisLefebvre\Bundle\AsyncTweetsBundle\Entity\User;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

class LoadUserData extends AbstractFixture
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user
            ->setId(12301465)
            ->setName('Asynchronous tweets')
            ->setScreenName('AsyncTweets')
            ->setProfileImageUrlHttps('https://abs.twimg.com/sticky/default_profile_images/default_profile_5_normal.png');

        $manager->persist($user);
        $manager->flush();

        $this->addReference('user', $user);

        // User who retweet
        $user = new User();
        $user
            ->setId(13334762)
            ->setName('GitHub')
            ->setScreenName('github')
            ->setProfileImageUrlHttps('https://pbs.twimg.com/profile_images/616309728688238592/pBeeJQDQ_normal.png');

        $manager->persist($user);
        $manager->flush();

        $this->addReference('user-github', $user);

        // User whose tweet is retweeted
        $user = new User();
        $user
            ->setId(131295561)
            ->setName('GitHub Engineering')
            ->setScreenName('GitHubEng')
            ->setProfileImageUrlHttps('https://pbs.twimg.com/profile_images/593061696039706627/uzIQ4lJF_normal.png');

        $manager->persist($user);
        $manager->flush();

        $this->addReference('user-githubeng', $user);
    }
}
