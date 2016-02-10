<?php

namespace AlexisLefebvre\Bundle\AsyncTweetsBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use AlexisLefebvre\Bundle\AsyncTweetsBundle\Entity\Media;

class LoadMediaData extends AbstractFixture implements DependentFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $media = new Media();
        $media
            ->setId(42003968)
            ->setMediaUrlHttps('http://pbs.twimg.com/media/B-FcA_4IQAAErQF.jpg')
            ->setUrl('http://t.co/rX1oieH1ug')
            ->setDisplayUrl('pic.twitter.com/rX1oieH1ug')
            ->setExpandedUrl('http://twitter.com/AsyncTweets/status/567836201210900480/photo/1')
        ;
        
        $manager->persist($media);
        $manager->flush();
        
        $this->addReference('media-1', $media);
        
        $media = new Media();
        $media
            ->setId(42003969)
            ->setMediaUrlHttps('http://pbs.twimg.com/media/B-FcA_4IQAAErQF.jpg')
            ->setUrl('http://t.co/rX1oieH1ug')
            ->setDisplayUrl('pic.twitter.com/rX1oieH1ug')
            ->setExpandedUrl('http://twitter.com/AsyncTweets/status/567836201210900480/photo/1')
        ;
        
        $manager->persist($media);
        $manager->flush();
        
        $this->addReference('media-2', $media);
    }

    /**
     * {@inheritDoc}
     */
    public function getDependencies()
    {
        return array('AlexisLefebvre\Bundle\AsyncTweetsBundle\DataFixtures\ORM\LoadUserData');
    }
}
