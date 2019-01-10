@managing_specials
Feature: Deleting multiple specials
    In order to remove test, obsolete or incorrect specials in an efficient way
    As an Administrator
    I want to be able to delete multiple specials at once from the registry

    Background:
        Given the store operates on a single channel in "United States"
        And there is a special "Christmas sale"
        And there is also a special "New Year sale"
        And there is also a special "Easter sale"
        And I am logged in as an administrator

    @ui @javascript
    Scenario: Deleting multiple specials at once
        When I browse specials
        And I check the "Christmas sale" special
        And I check also the "New Year sale" special
        And I delete them
        Then I should be notified that they have been successfully deleted
        And I should see a single special in the list
        And I should see the special "Easter sale" in the list
