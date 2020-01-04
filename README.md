# AsyncTweetsBundle

A Symfony bundle providing a Twitter reader for asynchronous reading

[Packagist ![Latest Stable Version][Packagist Stable Image] ![Latest Unstable Version][Packagist Unstable Image]][Packagist]

Builds: 
[![Build status][Travis Master image]][Travis Master]
[![AppVeyor][AppVeyor image]][AppVeyor]
[![Circle CI][Circle CI image]][Circle CI]

Code analysis:
[![Scrutinizer Code Quality][Scrutinizer image]
![Scrutinizer][Scrutinizer Coverage Image]][Scrutinizer]
[![Coveralls][Coveralls image]][Coveralls]
[![Code Climate][Code Climate image]][Code Climate]
[![Codacy][Codacy image]][Codacy]
[![StyleCI][StyleCI image]][StyleCI]
[![SensioLabsInsight][SensioLabsInsight Image]][SensioLabsInsight]

## Links

 - Demo: http://asynctweets.alexislefebvre.com/demo/
 - Code coverage: http://asynctweets.alexislefebvre.com/codecoverage/
 - Doxygen: http://asynctweets.alexislefebvre.com/doxygen/
 - ApiGen: http://asynctweets.alexislefebvre.com/apigen/

## Goal

The goal of this project is to create an online Twitter reader, built with [Symfony][Symfony].
AsyncTweets retrieves and stores your timeline, allowing to read your Twitter timeline even if you're away from your Twitter client for several days.

This bundle is also used to test several CI (Continuous Integration) services.

## Features

 - Retrieve tweets by using User's Twitter keys
 - Display the tweets with a pagination
 - Display images below tweets

## Installation

### Requirements:

 - [Twitter keys][Twitter keys]
 - PHP >= 7.1
 - a database (must be supported by Doctrine2)
 - [Symfony][Symfony GitHub] (3.4+) with [Composer][Composer]. If you want to install it:

        php composer.phar create-project symfony/framework-standard-edition YOUR_DIRECTORY "3.4.*" -vvv

### Steps:
 
 1. Install this bundle with Composer: `cd YOUR_DIRECTORY ; php composer.phar require alexislefebvre/async-tweets-bundle dev-master --prefer-dist -vvv`
 2. Add the bundle in <kbd>app/AppKernel.php</kbd>:

        <?php
        
        public function registerBundles()
        {
            $bundles = array(
                // ...
                new AlexisLefebvre\Bundle\AsyncTweetsBundle\AsyncTweetsBundle(),
            );
        }

 3. Enter your Twitter keys at the end of the <kbd>app/config/parameters.yml</kbd> file:

        twitter_consumer_key: null
        twitter_consumer_secret: null
        twitter_token: null
        twitter_token_secret: null

 4. Create the database and create the tables: `php app/console doctrine:schema:update --force --env=prod`

### Usage:

 1. Launch this command to fetch tweets: `php app/console statuses:hometimeline --table --env=prod`, with the ` --table` option the imported tweets will be shown
 2. Import the routes in your <kbd>app/config/routing.yml</kbd>:
 
        asynctweets_website:
            resource: "@AsyncTweetsBundle/Resources/config/routing.yml"
            prefix:   /asynctweets # Use only "/" if you want AsyncTweets at the root of the website

 3. Open the page with your browser `.../YOUR_DIRECTORY/web/asynctweets/` or use the following command `php app/console statuses:read --env=prod` to see tweets
 4. Add `php app/console statuses:hometimeline --env=prod` in your crontab (e.g. every hour) to retrieve tweets automatically

## Dependencies
 - [symfony/symfony][Symfony GitHub] (3.4+)
 - [abraham/twitteroauth][twitteroauth] (^0.6.0)
 - [twitter/bootstrap][Twitter Bootstrap] (use [Bootswatch 3.3.2][Bootstrap CDN])


### Tests:

    php vendor/bin/phpunit 

[Packagist]: https://packagist.org/packages/alexislefebvre/async-tweets-bundle
[Packagist Stable Image]: https://poser.pugx.org/alexislefebvre/async-tweets-bundle/v/stable.svg
[Packagist Unstable Image]: https://poser.pugx.org/alexislefebvre/async-tweets-bundle/v/unstable.svg

[Symfony]: http://symfony.com/
[Twitter keys]: https://apps.twitter.com/
[Symfony GitHub]: https://github.com/symfony/symfony
[Composer]: https://getcomposer.org/download/

[Travis Master image]: https://travis-ci.org/alexislefebvre/AsyncTweetsBundle.svg?branch=master
[Travis Master]: https://travis-ci.org/alexislefebvre/AsyncTweetsBundle
[AppVeyor image]: https://ci.appveyor.com/api/projects/status/p3n423qlvnrkabg3/branch/master?svg=true
[AppVeyor]: https://ci.appveyor.com/project/alexislefebvre/asynctweetsbundle/branch/master
[Circle CI image]: https://circleci.com/gh/alexislefebvre/AsyncTweetsBundle/tree/master.svg?style=shield&circle-token=c02b18cc286ccd9420065675d92a2574524c5939
[Circle CI]: https://circleci.com/gh/alexislefebvre/AsyncTweetsBundle/tree/master

[Scrutinizer image]: https://scrutinizer-ci.com/g/alexislefebvre/AsyncTweetsBundle/badges/quality-score.png?b=master
[Scrutinizer]: https://scrutinizer-ci.com/g/alexislefebvre/AsyncTweetsBundle/?branch=master
[Scrutinizer Coverage image]: https://scrutinizer-ci.com/g/alexislefebvre/AsyncTweetsBundle/badges/coverage.png?b=master
[Coveralls image]: https://coveralls.io/repos/github/alexislefebvre/AsyncTweetsBundle/badge.svg?branch=master
[Coveralls]: https://coveralls.io/github/alexislefebvre/AsyncTweetsBundle?branch=master
[Code Climate image]: https://codeclimate.com/github/alexislefebvre/AsyncTweetsBundle/badges/gpa.svg
[Code Climate]: https://codeclimate.com/github/alexislefebvre/AsyncTweetsBundle
[Codacy image]: https://api.codacy.com/project/badge/grade/0803f8e9a98c4abca2c9bcfe750e19c4
[Codacy]: https://www.codacy.com/app/alexislefebvre/AsyncTweetsBundle
[StyleCI image]: https://styleci.io/repos/33274240/shield
[StyleCI]: https://styleci.io/repos/33274240
[SensioLabsInsight Image]: https://insight.sensiolabs.com/projects/00d3eb84-0c1c-471c-9f76-d8abe41a647d/mini.png
[SensioLabsInsight]: https://insight.sensiolabs.com/projects/00d3eb84-0c1c-471c-9f76-d8abe41a647d

[twitteroauth]: https://github.com/abraham/twitteroauth
[Twitter Bootstrap]: https://github.com/twbs/bootstrap
[Bootstrap CDN]: http://www.bootstrapcdn.com/#bootswatch_tab
