@setono_sylius_bulk_specials_managing_specials
Feature: Adding a new special
    In order to sell more by creating discount incentives for customers
    As an Administrator
    I want to add a new special

    Background:
        Given the store operates on a single channel in "United States"
        And I am logged in as an administrator

    @ui
    Scenario: Adding a new special
        Given I want to create a new special
        When I specify its code as "ABSTRACT_SPECIAL"
        And I name it "Abstract special"
        And I add it
        Then I should be notified that it has been successfully created
        And the "Abstract special" special should appear in the registry

    @ui
    Scenario: Adding a new exclusive special
        Given I want to create a new special
        When I specify its code as "ABSTRACT_SPECIAL"
        And I name it "Abstract special"
        And I make it exclusive
        And I add it
        Then I should be notified that it has been successfully created
        And the "Abstract special" special should be exclusive

    @ui
    Scenario: Adding a new channels special
        Given I want to create a new special
        When I specify its code as "ABSTRACT_SPECIAL"
        And I name it "Abstract special"
        And I make it applicable for the "United States" channel
        And I add it
        Then I should be notified that it has been successfully created
        And the "Abstract special" special should be applicable for the "United States" channel

    @ui
    Scenario: Adding a special with start and end date
        Given I want to create a new special
        When I specify its code as "ABSTRACT_SPECIAL"
        And I name it "Abstract special"
        And I make it available from "21.04.2017" to "21.05.2017"
        And I add it
        Then I should be notified that it has been successfully created
