<?php

namespace AsyncTweets\AsyncTweetsBundle\Tests\Entity;

use AsyncTweets\AsyncTweetsBundle\Entity\User;

class UsertTest extends \PHPUnit_Framework_TestCase
{
    public function testUser()
    {
        $user = new User();
        $user
            ->setId(90556897)
            ->setName('Twitter France')
            ->setScreenName('TwitterFrance')
            ->setProfileImageUrl('http://abs.twimg.com/sticky/default_profile_images/default_profile_5_normal.png')
        ;
        
        $this->assertEquals(
            90556897,
            $user->getId()
        );
        
        $this->assertEquals(
            'Twitter France',
            $user->getName()
        );
        
        $this->assertEquals(
            'TwitterFrance',
            $user->getScreenName()
        );
        
        $this->assertEquals(
            'http://abs.twimg.com/sticky/default_profile_images/default_profile_5_normal.png',
            $user->getProfileImageUrl()
        );
    }
}

