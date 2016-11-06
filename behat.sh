#!/bin/bash

php Tests/App/console doctrine:database:drop --env=test --force
php Tests/App/console doctrine:schema:update --env=test --force

php vendor/bin/behat
