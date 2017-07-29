<?php

namespace AlexisLefebvre\Bundle\AsyncTweetsBundle\Tests\Features\Context;

use AlexisLefebvre\Bundle\AsyncTweetsBundle\Entity\Tweet;
use Behat\Behat\Context\Context;

class FeatureContext implements Context
{
    /** @var $now \Datetime */
    private $now;
    /** @var $tweet \AlexisLefebvre\Bundle\AsyncTweetsBundle\Entity\Tweet */
    private $tweet;

    /**
     * Initializes context.
     */
    public function __construct()
    {
    }

    /**
     * @Given there is a Tweet
     */
    public function thereIsATweet()
    {
        $this->now = new \Datetime('now');

        $this->tweet = new Tweet(152120320);
        $this->tweet
            ->setCreatedAt($this->now)
            ->setText('Hello World!')
            ->setRetweetCount(1999)
            ->setFavoriteCount(42)
            ->setInTimeline(true);
    }

    /**
     * @Then the Tweet must have correct id
     */
    public function theTweetMustHaveCorrectId()
    {
        \PHPUnit\Framework\Assert::assertSame(
            152120320,
            $this->tweet->getId()
        );
    }

    /**
     * @Then the Tweet must have correct created at date
     */
    public function theTweetMustHaveCorrectCreatedAtDate()
    {
        \PHPUnit\Framework\Assert::assertSame(
            $this->now,
            $this->tweet->getCreatedAt()
        );
    }

    /**
     * @Then the Tweet must have correct text
     */
    public function theTweetMustHaveCorrectText()
    {
        \PHPUnit\Framework\Assert::assertSame(
            'Hello World!',
            $this->tweet->getText()
        );
    }

    /**
     * @Then the Tweet must have correct retweet count
     */
    public function theTweetMustHaveCorrectRetweetCount()
    {
        \PHPUnit\Framework\Assert::assertSame(
            1999,
            $this->tweet->getRetweetCount()
        );
    }

    /**
     * @Then the Tweet must have correct favorite count
     */
    public function theTweetMustHaveCorrectFavoriteCount()
    {
        \PHPUnit\Framework\Assert::assertSame(
            42,
            $this->tweet->getFavoriteCount()
        );
    }

    /**
     * @Then the Tweet must be in timeline
     */
    public function theTweetMustBeInTimeline()
    {
        \PHPUnit\Framework\Assert::assertTrue(
            $this->tweet->isInTimeline()
        );
    }
}
