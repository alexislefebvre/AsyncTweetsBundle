# AsyncTweetsBundle

A Symfony bundle providing a Twitter reader for asynchronous reading

[Packagist ![Latest Stable Version][Packagist Stable Image] ![Latest Unstable Version][Packagist Unstable Image]][Packagist]

Builds: 
[![GA status][GitHub Actions image]][GitHub Actions]
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
[![SymfonyInsight][SymfonyInsight Image]][SymfonyInsight]

## Links

 - Demo: https://asynctweets.alexislefebvre.com/demo/
 - Code coverage: https://asynctweets.alexislefebvre.com/codecoverage/
 - Doxygen: https://asynctweets.alexislefebvre.com/doxygen/
 - ApiGen: https://asynctweets.alexislefebvre.com/apigen/

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
 - PHP >= 7.2
 - a database (must be supported by Doctrine2)
 - [Symfony][Symfony GitHub] (3.4+) with [Composer][Composer]

### Steps:
 
 1. Install this bundle with Composer: `composer require alexislefebvre/async-tweets-bundle`
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

    make tests

### Quality Assurance:

    make qa

[Packagist]: https://packagist.org/packages/alexislefebvre/async-tweets-bundle
[Packagist Stable Image]: https://poser.pugx.org/alexislefebvre/async-tweets-bundle/v/stable.svg
[Packagist Unstable Image]: https://poser.pugx.org/alexislefebvre/async-tweets-bundle/v/unstable.svg

[Symfony]: https://symfony.com/
[Twitter keys]: https://apps.twitter.com/
[Symfony GitHub]: https://github.com/symfony/symfony
[Composer]: https://getcomposer.org/download/

[GitHub Actions image]: https://github.com/alexislefebvre/AsyncTweetsBundle/actions/workflows/tests.yml/badge.svg
[GitHub Actions]: https://github.com/alexislefebvre/AsyncTweetsBundle/actions/workflows/tests.yml

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
[SymfonyInsight Image]: https://insight.symfony.com/projects/22448dd4-13ca-49ef-af7d-5f5bff1b3053/mini.svg
[SymfonyInsight]: https://insight.symfony.com/projects/22448dd4-13ca-49ef-af7d-5f5bff1b3053

[twitteroauth]: https://github.com/abraham/twitteroauth
[Twitter Bootstrap]: https://github.com/twbs/bootstrap
[Bootstrap CDN]: https://www.bootstrapcdn.com/bootswatch/
