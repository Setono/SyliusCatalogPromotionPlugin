@setono_sylius_catalog_promotion_managing_discounts
Feature: Editing discount
  In order to change discount details
  As an Administrator
  I want to be able to edit a discount

  Background:
    Given the store operates on a single channel in "United States"
    And there is a discount "Christmas sale" with priority 0
    And there is a discount "Holiday sale" with priority 1
    And I am logged in as an administrator

  @ui
  Scenario: Seeing disabled code field when editing discount
    When I want to modify a "Christmas sale" discount
    Then the code field should be disabled

  @ui
  Scenario: Editing discount exclusiveness
    Given I want to modify a "Christmas sale" discount
    When I make it exclusive
    And I save my changes
    Then I should be notified that it has been successfully edited
    And the "Christmas sale" discount should be exclusive

  @ui
  Scenario: Editing discounts channels
    Given I want to modify a "Christmas sale" discount
    When I make it applicable for the "United States" channel
    And I save my changes
    Then I should be notified that it has been successfully edited
    And the "Christmas sale" discount should be applicable for the "United States" channel

  @ui
  Scenario: Editing a discount with start and end date
    Given I want to modify a "Christmas sale" discount
    When I make it available from "12.12.2017" to "24.12.2017"
    And I save my changes
    Then I should be notified that it has been successfully edited
    And the "Christmas sale" discount should be available from "12.12.2017" to "24.12.2017"

  @ui
  Scenario: Editing discount after adding a new channel
    Given this discount gives "10%" discount
    When the store also operates on another channel named "EU-WEB"
    Then I should be able to modify a "Christmas sale" discount

  @ui
  Scenario: Remove priority from existing discount
    Given I want to modify a "Christmas sale" discount
    When I remove its priority
    And I save my changes
    Then I should be notified that it has been successfully edited
    And the "Christmas sale" discount should have priority 1
