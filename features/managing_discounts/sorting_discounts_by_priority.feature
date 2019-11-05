@setono_sylius_bulk_discount_managing_discounts
Feature: Sorting listed discounts by priority
    In order to change the order by which discounts are used
    As an Administrator
    I want to sort discounts by their priority

    Background:
        Given the store operates on a single channel in "United States"
        And there is a discount "Honour Harambe" with priority 2
        And there is a discount "Gimme An Owl" with priority 1
        And there is a discount "Pugs For Everyone" with priority 0
        And I am logged in as an administrator

    @ui
    Scenario: discounts are sorted by priority in descending order by default
        When I want to browse discounts
        Then I should see 3 discounts on the list
        And the first discount on the list should have name "Honour Harambe"
        And the last discount on the list should have name "Pugs For Everyone"

    @ui
    Scenario: discount's default priority is 0 which puts it at the bottom of the list
        Given there is a discount "Flying Pigs"
        When I want to browse discounts
        Then I should see 4 discounts on the list
        And the last discount on the list should have name "Flying Pigs"

    @ui
    Scenario: discount added with priority -1 is set at the top of the list
        Given there is a discount "Flying Pigs" with priority -1
        When I want to browse discounts
        Then I should see 4 discounts on the list
        And the first discount on the list should have name "Flying Pigs"
