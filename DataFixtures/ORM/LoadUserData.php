<?php

namespace AlexisLefebvre\Bundle\AsyncTweetsBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use AlexisLefebvre\Bundle\AsyncTweetsBundle\Entity\User;

class LoadUserData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user
            ->setId(12301465)
            ->setName('Asynchronous tweets')
            ->setScreenName('AsyncTweets')
            ->setProfileImageUrl('http://abs.twimg.com/sticky/default_profile_images/default_profile_5_normal.png')
        ;

        $manager->persist($user);
        $manager->flush();
        
        $this->addReference('user', $user);
        
        // User who retweet
        $user = new User();
        $user
            ->setId(13334762)
            ->setName('GitHub')
            ->setScreenName('github')
            ->setProfileImageUrl('http://pbs.twimg.com/profile_images/616309728688238592/pBeeJQDQ_normal.png')
        ;

        $manager->persist($user);
        $manager->flush();
        
        $this->addReference('user-github', $user);
        
        // Use whose tweet is retweeted
        $user = new User();
        $user
            ->setId(131295561)
            ->setName('GitHub Engineering')
            ->setScreenName('GitHubEng')
            ->setProfileImageUrl('http://pbs.twimg.com/profile_images/593061696039706627/uzIQ4lJF_normal.png')
        ;

        $manager->persist($user);
        $manager->flush();
        
        $this->addReference('user-githubeng', $user);
    }
    
    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 1; // the order in which fixtures will be loaded
    }
}
