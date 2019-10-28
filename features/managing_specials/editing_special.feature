@setono_sylius_bulk_discount_managing_specials
Feature: Editing special
    In order to change special details
    As an Administrator
    I want to be able to edit a special

    Background:
        Given the store operates on a single channel in "United States"
        And there is a special "Christmas sale" with priority 0
        And there is a special "Holiday sale" with priority 1
        And I am logged in as an administrator

    @ui
    Scenario: Seeing disabled code field when editing special
        When I want to modify a "Christmas sale" special
        Then the code field should be disabled

    @ui
    Scenario: Editing special exclusiveness
        Given I want to modify a "Christmas sale" special
        When I make it exclusive
        And I save my changes
        Then I should be notified that it has been successfully edited
        And the "Christmas sale" special should be exclusive

    @ui
    Scenario: Editing specials channels
        Given I want to modify a "Christmas sale" special
        When I make it applicable for the "United States" channel
        And I save my changes
        Then I should be notified that it has been successfully edited
        And the "Christmas sale" special should be applicable for the "United States" channel

    @ui
    Scenario: Editing a special with start and end date
        Given I want to modify a "Christmas sale" special
        When I make it available from "12.12.2017" to "24.12.2017"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And the "Christmas sale" special should be available from "12.12.2017" to "24.12.2017"

    @ui
    Scenario: Editing special after adding a new channel
        Given this special gives "10%" discount
        When the store also operates on another channel named "EU-WEB"
        Then I should be able to modify a "Christmas sale" special

    @ui
    Scenario: Remove priority from existing special
        Given I want to modify a "Christmas sale" special
        When I remove its priority
        And I save my changes
        Then I should be notified that it has been successfully edited
        And the "Christmas sale" special should have priority 1
