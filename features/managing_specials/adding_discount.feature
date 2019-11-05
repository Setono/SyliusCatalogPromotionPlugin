@setono_sylius_bulk_discount_managing_discounts
Feature: Adding a new discount
  In order to sell more by creating discount incentives for customers
  As an Administrator
  I want to add a new discount

  Background:
    Given the store operates on a single channel in "United States"
    And I am logged in as an administrator

  @ui
  Scenario: Adding a new discount
    Given I want to create a new discount
    When I specify its code as "ABSTRACT_SPECIAL"
    And I name it "Abstract special"
    And I specify 20% action percent
    And I add it
    Then I should be notified that it has been successfully created
    And the "Abstract special" discount should appear in the registry

  @ui
  Scenario: Adding a new exclusive discount
    Given I want to create a new discount
    When I specify its code as "ABSTRACT_SPECIAL"
    And I name it "Abstract special"
    And I make it exclusive
    And I specify 20% action percent
    And I add it
    Then I should be notified that it has been successfully created
    And the "Abstract special" discount should be exclusive

  @ui
  Scenario: Adding a new channels discount
    Given I want to create a new discount
    When I specify its code as "ABSTRACT_SPECIAL"
    And I name it "Abstract special"
    And I make it applicable for the "United States" channel
    And I specify 20% action percent
    And I add it
    Then I should be notified that it has been successfully created
    And the "Abstract special" discount should be applicable for the "United States" channel

  @ui
  Scenario: Adding a discount with start and end date
    Given I want to create a new discount
    When I specify its code as "ABSTRACT_SPECIAL"
    And I name it "Abstract special"
    And I make it available from "21.04.2017" to "21.05.2017"
    And I specify 20% action percent
    And I add it
    Then I should be notified that it has been successfully created
