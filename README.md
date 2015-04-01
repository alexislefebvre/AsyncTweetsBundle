# AsyncTweets [![Build status][Master image]][Master] [![Scrutinizer Code Quality][Scrutinizer image]][Scrutinizer]

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
 - PHP >= 5.4 (required by abraham/twitteroauth[0.5.0])
 - a database (must be supported by Doctrine2)

### Steps:
 
 1. Clone this repository
 2. Install [Composer][Composer] (`php -r "readfile('https://getcomposer.org/installer');" | php`)
 3. Install the vendors: `php composer.phar install --prefer-dist --no-dev -vvv --profile` and enter your Twitter keys at the end of the installation wizard (you can still add the keys later by editing the `app/config/parameters.yml` file)
 4. Create the database and create the tables: `php app/console doctrine:schema:update --force --env=prod`
 5. Launch this command to fetch tweets: `php app/console statuses:hometimeline --table`, with the ` --table` option the imported tweets will be shown
 6. Open the page with your browser `.../AsyncTweets/web/` or use the following command `php app/console statuses:read --env=prod` to see tweets
 7. Add `php app/console statuses:hometimeline` in your crontab (e.g. every hour) to retrieve tweets automatically

### Tests:

`./phpunit.sh` or `phpunit -c app/phpunit.xml.dist`

## Dependencies

 - [symfony/symfony][Symfony2 GitHub] (2.6)
 - [abraham/twitteroauth][twitteroauth] (0.5.0)
 
### Development environment

 - [doctrine/doctrine-fixtures-bundle][doctrine-fixtures-bundle] (~2.2)
 - [liip/functional-test-bundle][functional-test-bundle] (~1.0)

[Master image]: https://travis-ci.org/alexislefebvre/AsyncTweets.svg?branch=master
[Master]: https://travis-ci.org/alexislefebvre/AsyncTweets
[Scrutinizer image]: https://scrutinizer-ci.com/g/alexislefebvre/AsyncTweets/badges/quality-score.png?b=master
[Scrutinizer]: https://scrutinizer-ci.com/g/alexislefebvre/AsyncTweets/?branch=master
[Symfony2]: http://symfony.com/
[Twitter keys]: https://apps.twitter.com/
[Composer]: https://getcomposer.org/download/
[Symfony2 GitHub]: https://github.com/symfony/symfony
[twitteroauth]: https://github.com/abraham/twitteroauth
[doctrine-fixtures-bundle]: https://github.com/doctrine/DoctrineFixturesBundle
[functional-test-bundle]: https://github.com/liip/LiipFunctionalTestBundle
