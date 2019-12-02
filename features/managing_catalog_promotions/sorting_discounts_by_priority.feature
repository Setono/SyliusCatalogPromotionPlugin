@managing_catalog_promotions
Feature: Sorting listed catalog promotions by priority
    In order to change the order by which catalog promotions are used
    As an Administrator
    I want to sort catalog promotions by their priority

    Background:
        Given the store operates on a single channel in "United States"
        And there is a catalog promotion "Honour Harambe" with priority 2
        And there is a catalog promotion "Gimme An Owl" with priority 1
        And there is a catalog promotion "Pugs For Everyone" with priority 0
        And I am logged in as an administrator

    @ui
    Scenario: discounts are sorted by priority in descending order by default
        When I want to browse catalog promotions
        Then I should see 3 catalog promotions on the list
        And the first catalog promotion on the list should have name "Honour Harambe"
        And the last catalog promotion on the list should have name "Pugs For Everyone"

    @ui
    Scenario: discount's default priority is 0 which puts it at the bottom of the list
        Given there is a catalog promotion "Flying Pigs"
        When I want to browse catalog promotions
        Then I should see 4 catalog promotions on the list
        And the last catalog promotion on the list should have name "Flying Pigs"

    @ui
    Scenario: discount added with priority -1 is set at the top of the list
        Given there is a catalog promotion "Flying Pigs" with priority -1
        When I want to browse catalog promotions
        Then I should see 4 catalog promotions on the list
        And the first catalog promotion on the list should have name "Flying Pigs"
