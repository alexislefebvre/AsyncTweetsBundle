# AsyncTweetsBundle [![Build status][Travis Master image]][Travis Master] [![Scrutinizer Code Quality][Scrutinizer image]][Scrutinizer] [![Dependency Status][Dependency Status Image]][Dependency Status] [![Coverage Status][Coverage Status Image]][Coverage Status] [![SensioLabsInsight][SensioLabsInsight Image]][SensioLabsInsight]

Symfony2 bundle providing a Twitter reader for asynchronous reading

[Packagist][Packagist]

## Links

Demo: http://asynctweets.alexislefebvre.com/demo/

Note: on the demo, the last tweet of a page will be shown on the "*Next »*" page because of a 32-bit PHP version.
The last tweet of a page won't be shown on the "*Next »*" page on a 64-bit PHP version.

Code coverage: http://asynctweets.alexislefebvre.com/codecoverage/

## Goal

The goal of this project is to create an online Twitter reader built with [Symfony2][Symfony2].
AsyncTweets retrieves and stores your timeline, allowing to read your Twitter timeline even if you're away from your Twitter client for several days.

## Features

 - Retrieve tweets by using User's Twitter keys
 - Display the tweets with a pagination
 - Display images below tweets

## Installation

### Requirements:

 - [Twitter keys][Twitter keys]
 - PHP >= 5.4 (required by abraham/twitteroauth 0.5.3)
 - a database (must be supported by Doctrine2)

### Steps:
 
 1. Install [Symfony 2.6][Symfony2 GitHub]
 2. Install this bundle with Composer: `composer require alexislefebvre/async-tweets-bundle --prefer-dist --no-dev -vvv --profile`
 3. Enter your Twitter keys at the end of the <kbd>app/config/parameters.yml</kbd> file:

        twitter_consumer_key: null
        twitter_consumer_secret: null
        twitter_token: null
        twitter_token_secret: null
   
 4. Create the database and create the tables: `php app/console doctrine:schema:update --force`
 5. Launch this command to fetch tweets: `php app/console statuses:hometimeline --table`, with the ` --table` option the imported tweets will be shown
 6. Import the routes in your <kbd>app/config/routing.yml</kbd>:
 
        asynctweets_website:
            resource: "@AsyncTweetsBundle/Resources/config/routing.yml"
            prefix:   /asynctweets

 7. Open the page with your browser `.../AsyncTweets/web/asynctweets/` or use the following command `php app/console statuses:read` to see tweets
 8. Add `php app/console statuses:hometimeline` in your crontab (e.g. every hour) to retrieve tweets automatically

### Tests:

`phpunit` or `phpunit -c app/phpunit.xml.dist`

## Dependencies

 - [symfony/symfony][Symfony2 GitHub] (2.6)
 - [abraham/twitteroauth][twitteroauth] (0.5.3)
 
### Development environment

 - [doctrine/doctrine-fixtures-bundle][doctrine-fixtures-bundle] (~2.2)
 - [liip/functional-test-bundle][functional-test-bundle] (~1.0)

[Packagist]: https://packagist.org/packages/alexislefebvre/async-tweets-bundle

[Symfony2]: http://symfony.com/
[Twitter keys]: https://apps.twitter.com/
[Symfony2 GitHub]: https://github.com/symfony/symfony

[Travis Master image]: https://travis-ci.org/alexislefebvre/AsyncTweetsBundle.svg?branch=master
[Travis Master]: https://travis-ci.org/alexislefebvre/AsyncTweetsBundle
[Scrutinizer image]: https://scrutinizer-ci.com/g/alexislefebvre/AsyncTweetsBundle/badges/quality-score.png?b=master
[Scrutinizer]: https://scrutinizer-ci.com/g/alexislefebvre/AsyncTweetsBundle/?branch=master
[Dependency Status Image]: https://www.versioneye.com/user/projects/5523d4ac971f7847ca0006cd/badge.svg?style=flat
[Dependency Status]: https://www.versioneye.com/user/projects/5523d4ac971f7847ca0006cd
[Coverage Status Image]: https://coveralls.io/repos/alexislefebvre/AsyncTweetsBundle/badge.svg
[Coverage Status]: https://coveralls.io/r/alexislefebvre/AsyncTweetsBundle
[SensioLabsInsight Image]: https://insight.sensiolabs.com/projects/00d3eb84-0c1c-471c-9f76-d8abe41a647d/mini.png
[SensioLabsInsight]: https://insight.sensiolabs.com/projects/00d3eb84-0c1c-471c-9f76-d8abe41a647d

[twitteroauth]: https://github.com/abraham/twitteroauth
[doctrine-fixtures-bundle]: https://github.com/doctrine/DoctrineFixturesBundle
[functional-test-bundle]: https://github.com/liip/LiipFunctionalTestBundle
