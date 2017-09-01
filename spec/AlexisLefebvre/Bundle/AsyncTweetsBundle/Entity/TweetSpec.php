<?php

namespace spec\AlexisLefebvre\Bundle\AsyncTweetsBundle\Entity;

use AlexisLefebvre\Bundle\AsyncTweetsBundle\Entity\Tweet;
use PhpSpec\Exception\Example\SkippingException;
use PhpSpec\ObjectBehavior;

class TweetSpec extends ObjectBehavior
{
    public function let(Tweet $tweet)
    {
        $this->beConstructedWith($tweet);

        $fakeTweet = new \stdClass();
        $fakeTweet->created_at = 'now';
        $fakeTweet->text = 'Hello Twitter! #myfirstTweet';
        $fakeTweet->retweet_count = 5;
        $fakeTweet->favorite_count = 12;

        $this->setValues($fakeTweet);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(Tweet::class);
    }

    public function it_should_have_a_created_at_datetime()
    {
        $this->getCreatedAt()->shouldHaveType(new \DateTime());
    }

    public function it_should_have_the_title()
    {
        $this->getText()->shouldBeEqualTo('Hello Twitter! #myfirstTweet');
    }

    public function it_should_have_retweet_count()
    {
        $this->getRetweetCount()->shouldBeEqualTo(5);
    }

    public function it_should_have_favorite_count()
    {
        $this->getFavoriteCount()->shouldBeEqualTo(12);
    }

    public function it_should_not_be_in_timeline()
    {
        $this->shouldNotBeInTimeline();
    }

    public function it_should_not_allow_invalid_medias()
    {
        /* @see https://github.com/phpspec/phpspec/issues/119#issuecomment-43436579 */
        if (version_compare(PHP_VERSION, '7', '<')) {
            throw new SkippingException('Unsupported type hinting with PHP < 7');
        }

        $this->shouldThrow('\TypeError')->during('addMedia', [null]);
        $this->shouldThrow('\TypeError')->during('removeMedia', [null]);
    }
}
