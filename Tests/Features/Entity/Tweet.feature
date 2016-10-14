Feature: Tweet entity
  In order to manage tweets,
  I need to be able to put a Tweet in the Tweet entity

  Scenario: Declaring a Tweet
    Given there is a Tweet
    Then the Tweet must have correct id
    Then the Tweet must have correct created at date
    Then the Tweet must have correct text
    Then the Tweet must have correct retweet count
    Then the Tweet must have correct favorite count
    Then the Tweet must be in timeline
