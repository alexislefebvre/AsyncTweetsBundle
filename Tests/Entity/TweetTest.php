<?php

namespace AlexisLefebvre\Bundle\AsyncTweetsBundle\Tests\Entity;

use AlexisLefebvre\Bundle\AsyncTweetsBundle\Entity\Media;
use AlexisLefebvre\Bundle\AsyncTweetsBundle\Entity\Tweet;
use AlexisLefebvre\Bundle\AsyncTweetsBundle\Entity\User;

class TweetTest extends \PHPUnit_Framework_TestCase
{
    public function testTweet()
    {
        $now = new \Datetime('now');
        
        # Media
        $media = new Media(42003968);
        $media
            ->setMediaUrlHttps('http://pbs.twimg.com/media/B-FcA_4IQAAErQF.jpg')
            ->setUrl('http://t.co/rX1oieH1ug')
            ->setDisplayUrl('pic.twitter.com/rX1oieH1ug')
            ->setExpandedUrl('http://twitter.com/AsyncTweets/status/567836201210900480/photo/1')
        ;
        
        # Tweet
        $tweet = new Tweet(152120320);
        $tweet
            ->setCreatedAt($now)
            ->setText('Hello World!')
            ->setRetweetCount(1999)
            ->setFavoriteCount(42)
            ->setInTimeline(true)
            ->addMedia($media)
        ;
        
        $this->assertSame(
            152120320,
            $tweet->getId()
        );
        
        $this->assertSame(
            $now,
            $tweet->getCreatedAt()
        );
        
        $this->assertSame(
            'Hello World!',
            $tweet->getText()
        );
        
        $this->assertSame(
            1999,
            $tweet->getRetweetCount()
        );
        
        $this->assertSame(
            42,
            $tweet->getFavoriteCount()
        );
        
        $this->assertTrue(
            $tweet->isInTimeline()
        );
        
        # Check Tweet associated to Media
        # Count Tweet associated to the User
        $this->assertSame(
            1,
            count($media->getTweets())
        );
        
        # Bind the Tweet to a User
        $user = new User(90556897);
        $user
            ->setName('Twitter France')
            ->setScreenName('TwitterFrance')
        ;
        
        $userClone = clone $user;
        
        $tweet
            ->setUser($user)
        ;
        
        $this->assertEquals(
            $userClone,
            $tweet->getUser()
        );
        
        # Count Tweet associated to the User
        $this->assertSame(
            1,
            count($user->getTweets())
        );
        
        $tweet->setInTimeline(false);
        
        $this->assertFalse(
            $tweet->isInTimeline()
        );
    }
}
