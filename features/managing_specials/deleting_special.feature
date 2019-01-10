@setono_sylius_bulk_specials_managing_specials
Feature: Deleting a special
    In order to remove test, obsolete or incorrect specials
    As an Administrator
    I want to be able to delete a special from the registry

    Background:
        Given the store operates on a single channel in "United States"
        And there is a special "Christmas sale"
        And I am logged in as an administrator

    @ui
    Scenario: Deleted special should disappear from the registry
        When I delete a "Christmas sale" special
        Then I should be notified that it has been successfully deleted
        And this special should no longer exist in the special registry
