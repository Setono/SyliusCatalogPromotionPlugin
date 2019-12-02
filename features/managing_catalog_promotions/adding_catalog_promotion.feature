@managing_catalog_promotions
Feature: Adding a new catalog promotion
  In order to sell more by creating discount incentives for customers
  As an Administrator
  I want to add a new catalog promotion

  Background:
    Given the store operates on a single channel in "United States"
    And I am logged in as an administrator

  @ui
  Scenario: Adding a new catalog promotion
    Given I want to create a new catalog promotion
    When I specify its code as "20_OFF"
    And I name it "20% off"
    And I specify 20% action percent
    And I add it
    Then I should be notified that it has been successfully created
    And the "20% off" catalog promotion should appear in the registry

  @ui
  Scenario: Adding a new exclusive discount
    Given I want to create a new catalog promotion
    When I specify its code as "20_OFF"
    And I name it "20% off"
    And I make it exclusive
    And I specify 20% action percent
    And I add it
    Then I should be notified that it has been successfully created
    And the "20% off" catalog promotion should be exclusive

  @ui
  Scenario: Adding a new channels discount
    Given I want to create a new catalog promotion
    When I specify its code as "20_OFF"
    And I name it "20% off"
    And I make it applicable for the "United States" channel
    And I specify 20% action percent
    And I add it
    Then I should be notified that it has been successfully created
    And the "20% off" catalog promotion should be applicable for the "United States" channel

  @ui
  Scenario: Adding a discount with start and end date
    Given I want to create a new catalog promotion
    When I specify its code as "20_OFF"
    And I name it "20% off"
    And I make it available from "21.04.2022" to "21.05.2022"
    And I specify 20% action percent
    And I add it
    Then I should be notified that it has been successfully created
