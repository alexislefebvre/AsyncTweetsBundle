<?php

namespace Acme\Entity;

use AlexisLefebvre\Bundle\AsyncTweetsBundle\Entity\User;

class UserTest extends \PHPUnit\Framework\TestCase
{
    public function testUser()
    {
        $user = new User();
        $user
            ->setId(90556897)
            ->setName('Twitter France')
            ->setScreenName('TwitterFrance')
            ->setProfileImageUrlHttps('https://abs.twimg.com/sticky/default_profile_images/default_profile_5_normal.png');

        $this->assertSame(
            90556897,
            $user->getId()
        );

        $this->assertSame(
            'Twitter France',
            $user->getName()
        );

        $this->assertSame(
            'TwitterFrance',
            $user->getScreenName()
        );

        $this->assertSame(
            'https://abs.twimg.com/sticky/default_profile_images/default_profile_5_normal.png',
            $user->getProfileImageUrlHttps()
        );
    }

    public function testUserGetProfileImageUrlHttpOrHttps()
    {
        $user = new User();
        $user
            ->setId(90556897)
            ->setName('Twitter France')
            ->setScreenName('TwitterFrance')
            ->setProfileImageUrl('http://abs.twimg.com/sticky/default_profile_images/default_profile_5_normal.png');

        $this->assertSame(
            'http://abs.twimg.com/sticky/default_profile_images/default_profile_5_normal.png',
            $user->getProfileImageUrlHttpOrHttps()
        );

        $user
            ->setProfileImageUrlHttps('https://abs.twimg.com/sticky/default_profile_images/default_profile_5_normal.png');

        $this->assertSame(
            'https://abs.twimg.com/sticky/default_profile_images/default_profile_5_normal.png',
            $user->getProfileImageUrlHttpOrHttps()
        );
    }
}
