Feature: Test DefaultController with Fixtures

  Background:
    Given I load the following fixtures:
      | User  |
      | Tweet |

  Scenario: Index
    When I am on "/"
    Then I should see a "html > body" element
    And I should see a "html > head > title" element
    And I should see "Home timeline" in the "html > head > title" element
    And I should see a "main.container > div.tweets" element
    And I should see 3 "main.container > div.tweets > div.media > blockquote.media-body" elements
    # Tweets
    # First tweet
    And I should see "Tweet 1" in the "main.container > div.tweets > div.media:nth-child(1) > blockquote.media-body > p" element
    And I should see "User 1" in the "main.container > div.tweets > div.media:nth-child(1) > blockquote.media-body > small > a:first-child" element
    # Retweet count
    And I should see "42" in the "main.container > div.tweets > div.media:nth-child(1) > blockquote.media-body > small > span.badge:nth-child(3)" element
    # Favorite count
    And I should see "13" in the "main.container > div.tweets > div.media:nth-child(1) > blockquote.media-body > small > span.badge:nth-child(4)" element
    # Second tweet
    And I should see "Tweet 2" in the "main.container > div.tweets > div.media:nth-child(2) > blockquote.media-body > p" element
    And I should see "User 2" in the "main.container > div.tweets > div.media:nth-child(2) > blockquote.media-body > small > a" element
    # Third tweet
    And I should see "Tweet 3" in the "main.container > div.tweets > div.media:nth-child(3) > blockquote.media-body > p" element
    And I should see "User 3" in the "main.container > div.tweets > div.media:nth-child(3) > blockquote.media-body > small > a" element
    And I should see "3 pending tweets" in the "body > main.container > div.navigation.row > div.count.alert.alert-info" element
    # There is no message about deleted tweets
    And I should not see a "div.alert.alert-success" element
    When I follow the 2nd "Mark as read" link
    Then I should not see a "div.alert.alert-success" element
    # Delete old tweets
    When I follow "Delete old tweets"
    Then I should see a "div.alert.alert-success" element
    And I should see "1 tweets deleted." in the "div.alert.alert-success" element
    And I should see a "main.container > div.tweets" element
    And I should see 2 "main.container > div.tweets > div.media > blockquote.media-body" elements
    When I reload the page
    Then I should not see a "div.alert.alert-success" element
    And I should see 2 "main.container > div.tweets > div.media > blockquote.media-body" elements
