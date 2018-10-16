@managing_specials
Feature: Special unique code validation
    In order to uniquely identify specials
    As an Administrator
    I want to be prevented from adding two specials with the same code

    Background:
        Given the store operates on a single channel in "United States"
        And there is a special "Abstract special" identified by "ABSTRACT_SPECIAL" code
        And I am logged in as an administrator

    @ui
    Scenario: Trying to add special with taken code
        Given I want to create a new special
        When I specify its code as "ABSTRACT_SPECIAL"
        And I name it "Abstract special"
        And I try to add it
        Then I should be notified that special with this code already exists
        And there should still be only one special with code "ABSTRACT_SPECIAL"
