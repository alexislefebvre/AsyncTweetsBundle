# AsyncTweetsBundle

A Symfony2 bundle providing a Twitter reader for asynchronous reading

[![Build status][Travis Master image]][Travis Master] [![Scrutinizer Code Quality][Scrutinizer image]][Scrutinizer] [![Scrutinizer Coverage][Scrutinizer Coverage Image]][Scrutinizer Coverage] [![Dependency Status][Dependency Status Image]][Dependency Status] [![SensioLabsInsight][SensioLabsInsight Image]][SensioLabsInsight]

[Packagist][Packagist]

## Links

 - Demo: http://asynctweets.alexislefebvre.com/demo/
 - Code coverage: http://asynctweets.alexislefebvre.com/codecoverage/
 - Doxygen: http://asynctweets.alexislefebvre.com/doxygen/

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
 
 1. Install [Symfony 2][Symfony2 GitHub] (2.3 or higher) with [Composer][Composer]: `php composer.phar create-project symfony/framework-standard-edition YOUR_DIRECTORY "2.6.*" -vvv`
 2. Install this bundle with Composer: `cd YOUR_DIRECTORY ; php composer.phar require alexislefebvre/async-tweets-bundle dev-master --prefer-dist -vvv`
 3. Add the bundle in <kbd>app/AppKernel.php</kbd>:
 
        new AlexisLefebvre\Bundle\AsyncTweetsBundle\AsyncTweetsBundle(),

 4. Enter your Twitter keys at the end of the <kbd>app/config/parameters.yml</kbd> file:

        twitter_consumer_key: null
        twitter_consumer_secret: null
        twitter_token: null
        twitter_token_secret: null
   
 5. Create the database and create the tables: `php app/console doctrine:schema:update --force --env=prod`
 6. Launch this command to fetch tweets: `php app/console statuses:hometimeline --table --env=prod`, with the ` --table` option the imported tweets will be shown
 7. Import the routes in your <kbd>app/config/routing.yml</kbd>:
 
        asynctweets_website:
            resource: "@AsyncTweetsBundle/Resources/config/routing.yml"
            prefix:   /asynctweets # Use only "/" if you want AsyncTweets at the root of the website

 8. Open the page with your browser `.../YOUR_DIRECTORY/web/asynctweets/` or use the following command `php app/console statuses:read --env=prod` to see tweets
 9. Add `php app/console statuses:hometimeline --env=prod` in your crontab (e.g. every hour) to retrieve tweets automatically

## Dependencies
 - [symfony/symfony][Symfony2 GitHub] (2.3+)
 - [abraham/twitteroauth][twitteroauth] (0.5.3)
 - [twitter/bootstrap][Twitter Bootstrap] (use [Bootswatch 3.3.2][Bootstrap CDN])


### Tests:

If `phpunit` is installed:

    phpunit

Or by installing `phpunit` with Composer:

    php composer.phar require --dev phpunit/phpunit "4.6.*" -vvv ; php vendor/bin/phpunit

### Development environment

 - [doctrine/doctrine-fixtures-bundle][doctrine-fixtures-bundle] (~2.2)
 - [liip/functional-test-bundle][functional-test-bundle] (~1.0)

[Packagist]: https://packagist.org/packages/alexislefebvre/async-tweets-bundle

[Symfony2]: http://symfony.com/
[Twitter keys]: https://apps.twitter.com/
[Symfony2 GitHub]: https://github.com/symfony/symfony
[Composer]: https://getcomposer.org/download/

[Travis Master image]: https://travis-ci.org/alexislefebvre/AsyncTweetsBundle.svg?branch=master
[Travis Master]: https://travis-ci.org/alexislefebvre/AsyncTweetsBundle
[Scrutinizer image]: https://scrutinizer-ci.com/g/alexislefebvre/AsyncTweetsBundle/badges/quality-score.png?b=master
[Scrutinizer]: https://scrutinizer-ci.com/g/alexislefebvre/AsyncTweetsBundle/?branch=master
[Scrutinizer Coverage image]: https://scrutinizer-ci.com/g/alexislefebvre/AsyncTweetsBundle/badges/coverage.png?b=master
[Scrutinizer Coverage]: https://scrutinizer-ci.com/g/alexislefebvre/AsyncTweetsBundle/?branch=master
[Dependency Status Image]: https://www.versioneye.com/user/projects/5523d4ac971f7847ca0006cd/badge.svg?style=flat
[Dependency Status]: https://www.versioneye.com/user/projects/5523d4ac971f7847ca0006cd
[SensioLabsInsight Image]: https://insight.sensiolabs.com/projects/00d3eb84-0c1c-471c-9f76-d8abe41a647d/mini.png
[SensioLabsInsight]: https://insight.sensiolabs.com/projects/00d3eb84-0c1c-471c-9f76-d8abe41a647d
[twitteroauth]: https://github.com/abraham/twitteroauth
[Twitter Bootstrap]: https://github.com/twbs/bootstrap
[Bootstrap CDN]: http://www.bootstrapcdn.com/#bootswatch_tab
[doctrine-fixtures-bundle]: https://github.com/doctrine/DoctrineFixturesBundle
[functional-test-bundle]: https://github.com/liip/LiipFunctionalTestBundle
