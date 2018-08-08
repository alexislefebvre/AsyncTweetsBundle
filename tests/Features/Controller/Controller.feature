@reset-schema
Feature: Test DefaultController

  Scenario: Index
    When I am on the homepage
    Then I should see a "html > body" element
    And I should see a "html > head > title" element
    And I should see "Home timeline" in the "html > head > title" element
    And I should see a "main.container > div.tweets" element
    And I should see "0 pending tweets" in the "body > main.container > div.navigation.row > div.col-sm-7.col-xs-12.count.alert.alert-info" element
