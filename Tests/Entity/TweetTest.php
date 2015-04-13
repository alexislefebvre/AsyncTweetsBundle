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
        $media = new Media(567836200242003968);
        $media
            ->setMediaUrlHttps('http://pbs.twimg.com/media/B-FcA_4IQAAErQF.jpg')
            ->setUrl('http://t.co/rX1oieH1ug')
            ->setDisplayUrl('pic.twitter.com/rX1oieH1ug')
            ->setExpandedUrl('http://twitter.com/AsyncTweets/status/567836201210900480/photo/1')
        ;
        
        # Tweet
        $tweet = new Tweet(565939802152120320);
        $tweet
            ->setCreatedAt($now)
            ->setText('Hello World!')
            ->setRetweetCount(1999)
            ->setFavoriteCount(42)
            ->addMedia($media)
        ;
        
        $this->assertEquals(
            565939802152120320,
            $tweet->getId()
        );
        
        $this->assertEquals(
            $now,
            $tweet->getCreatedAt()
        );
        
        $this->assertEquals(
            'Hello World!',
            $tweet->getText()
        );
        
        $this->assertEquals(
            1999,
            $tweet->getRetweetCount()
        );
        
        $this->assertEquals(
            42,
            $tweet->getFavoriteCount()
        );
        
        # Check Tweet associated to Media
        # Count Tweet associated to the User
        $this->assertEquals(
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
        $this->assertEquals(
            1,
            count($user->getTweets())
        );
    }
}
