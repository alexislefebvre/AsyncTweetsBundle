<?php

namespace AlexisLefebvre\Bundle\AsyncTweetsBundle\Tests\Entity;

use AlexisLefebvre\Bundle\AsyncTweetsBundle\Entity\Media;

class MediaTest extends \PHPUnit\Framework\TestCase
{
    public function testTweet()
    {
        $media = new Media();
        $media
            ->setId(242003968)
            ->setMediaUrlHttps('http://pbs.twimg.com/media/B-FcA_4IQAAErQF.jpg')
            ->setUrl('http://t.co/rX1oieH1ug')
            ->setDisplayUrl('pic.twitter.com/rX1oieH1ug')
            ->setExpandedUrl('http://twitter.com/AsyncTweets/status/567836201210900480/photo/1');

        $this->assertSame(
            242003968,
            $media->getId()
        );

        $this->assertSame(
            'http://pbs.twimg.com/media/B-FcA_4IQAAErQF.jpg',
            $media->getMediaUrlHttps()
        );

        $this->assertSame(
            'http://t.co/rX1oieH1ug',
            $media->getUrl()
        );

        $this->assertSame(
            'pic.twitter.com/rX1oieH1ug',
            $media->getDisplayUrl()
        );

        $this->assertSame(
            'http://twitter.com/AsyncTweets/status/567836201210900480/photo/1',
            $media->getExpandedUrl()
        );
    }
}
