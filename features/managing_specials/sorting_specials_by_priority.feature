@managing_specials
Feature: Sorting listed specials by priority
    In order to change the order by which specials are used
    As an Administrator
    I want to sort specials by their priority

    Background:
        Given the store operates on a single channel in "United States"
        And there is a special "Honour Harambe" with priority 2
        And there is a special "Gimme An Owl" with priority 1
        And there is a special "Pugs For Everyone" with priority 0
        And I am logged in as an administrator

    @ui
    Scenario: specials are sorted by priority in descending order by default
        When I want to browse specials
        Then I should see 3 specials on the list
        And the first special on the list should have name "Honour Harambe"
        And the last special on the list should have name "Pugs For Everyone"

    @ui
    Scenario: special's default priority is 0 which puts it at the bottom of the list
        Given there is a special "Flying Pigs"
        When I want to browse specials
        Then I should see 4 specials on the list
        And the last special on the list should have name "Flying Pigs"

    @ui
    Scenario: special added with priority -1 is set at the top of the list
        Given there is a special "Flying Pigs" with priority -1
        When I want to browse specials
        Then I should see 4 specials on the list
        And the first special on the list should have name "Flying Pigs"
