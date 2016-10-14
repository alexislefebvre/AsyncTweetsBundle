Feature: Test DefaultController

  Scenario: Index
    When I visit the index page
    Then I should see a body tag
    And I should see the title of the page
    And I should see the pending tweets
