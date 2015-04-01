<?php

namespace AsyncTweets\AsyncTweetsBundle\Tests\Entity;

use AsyncTweets\AsyncTweetsBundle\Entity\Media;

class MediaTest extends \PHPUnit_Framework_TestCase
{
    public function testTweet()
    {
        $media = new Media();
        $media
            ->setId(567836200242003968)
            ->setMediaUrlHttps('http://pbs.twimg.com/media/B-FcA_4IQAAErQF.jpg')
            ->setUrl('http://t.co/rX1oieH1ug')
            ->setDisplayUrl('pic.twitter.com/rX1oieH1ug')
            ->setExpandedUrl('http://twitter.com/AsyncTweets/status/567836201210900480/photo/1')
        ;
        
        $this->assertEquals(
            567836200242003968,
            $media->getId()
        );
        
        $this->assertEquals(
            'http://pbs.twimg.com/media/B-FcA_4IQAAErQF.jpg',
            $media->getMediaUrlHttps()
        );
        
        $this->assertEquals(
            'http://t.co/rX1oieH1ug',
            $media->getUrl()
        );
        
        $this->assertEquals(
            'pic.twitter.com/rX1oieH1ug',
            $media->getDisplayUrl()
        );
        
        $this->assertEquals(
            'http://twitter.com/AsyncTweets/status/567836201210900480/photo/1',
            $media->getExpandedUrl()
        );
    }
}

