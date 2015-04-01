<?php

namespace AlexisLefebvre\Bundle\AsyncTweetsBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use AlexisLefebvre\Bundle\AsyncTweetsBundle\Entity\Media;

/**
 * @codeCoverageIgnore
 */
class LoadMediaData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $media = new Media();
        $media
            ->setId(567836200242003968)
            ->setMediaUrlHttps('http://pbs.twimg.com/media/B-FcA_4IQAAErQF.jpg')
            ->setUrl('http://t.co/rX1oieH1ug')
            ->setDisplayUrl('pic.twitter.com/rX1oieH1ug')
            ->setExpandedUrl('http://twitter.com/AsyncTweets/status/567836201210900480/photo/1')
        ;
        
        $manager->persist($media);
        $manager->flush();
        
        $this->addReference('media', $media);
    }
    
    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 10; // the order in which fixtures will be loaded
    }
}
