@setono_sylius_bulk_specials_managing_specials
Feature: Browsing specials
    In order to see all special
    As an Administrator
    I want to browse existing specials

    Background:
        Given the store operates on a single channel in "United States"
        And there is a special "Basic special"
        And I am logged in as an administrator

    @ui
    Scenario: Browsing specials
        Given I want to browse specials
        Then I should see a single special in the list
        And the "Basic special" special should exist in the registry
