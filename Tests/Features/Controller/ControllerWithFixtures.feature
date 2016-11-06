@alice(*)
Feature: Test DefaultController with Fixtures

  Scenario: Index
    When I am on the homepage
    Then I should see a "html > body" element
    And I should see a "html > head > title" element
    And I should see "Home timeline" in the "html > head > title" element
    And I should see a "main.container > div.tweets" element
    And I should see 3 "main.container > div.tweets > div.media > blockquote.media-body" elements
    And I should see "3 pending tweets" in the "body > main.container > div.navigation.row > div.count.alert.alert-info" element
    Then I should not see a "div.alert.alert-success" element
    When I press the 2nd "Mark as read" link
    Then I should not see a "div.alert.alert-success" element
    When I press the "Delete old tweets" link
    Then I should see a "div.alert.alert-success" element
    And I should see "1 tweets deleted." in the "div.alert.alert-success" element
    And I should see a "main.container > div.tweets" element
    And I should see 2 "main.container > div.tweets > div.media > blockquote.media-body" elements
    When I reload the page
    Then I should not see a "div.alert.alert-success" element
    And I should see 2 "main.container > div.tweets > div.media > blockquote.media-body" elements
