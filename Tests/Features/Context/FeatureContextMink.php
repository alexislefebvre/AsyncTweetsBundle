<?php

namespace AlexisLefebvre\Bundle\AsyncTweetsBundle\Tests\Features\Context;

use Behat\MinkExtension\Context\RawMinkContext;

/**
 * Class FeatureContextMink.
 *
 * @see https://github.com/jakzal/DemoBundle/blob/432e818097d9496223039eb28d79158daa216c6b/Features/Context/FeatureContext.php
 */
class FeatureContextMink extends RawMinkContext
{
    /**
     * Initializes context.
     */
    public function __construct()
    {
    }

    /**
     * @When I visit the index page
     */
    public function iVisitTheIndexPage()
    {
        $this->getSession()->visit($this->locatePath('/'));
    }

    /**
     * @Then I should see a body tag
     */
    public function iShouldSeeABodyTag()
    {
        $this->assertSession()->elementsCount(
            'css',
            'html > body',
            1
        );
    }

    /**
     * @Then I should see the title of the page
     */
    public function iShouldSeeTheTitleOfThePage()
    {
        $this->assertSession()->elementTextContains(
            'css',
            'html > head > title',
            'Home timeline'
        );
    }

    /**
     * @Then I should see the Tweets container
     */
    public function iShouldSeeTheTweetsContainer()
    {
        $this->assertSession()->elementsCount(
            'css',
            'main.container > div.tweets',
            1
        );
    }

    /**
     * @Then I should see the pending tweets
     */
    public function iShouldSeeThePendingTweets()
    {
        $this->assertSession()->elementTextContains(
            'css',
            'body > main.container > div.navigation.row > div.col-sm-7.col-xs-12.count.alert.alert-info',
            'pending tweets'
        );
    }
}
