@special_commands @cli
Feature: Special CLI feature
    In order to enable/disable specials via CLI
    As a Developer
    I want actual specials to be enabled and related products prices to be recalculated

    Background:
        Given the store operates on a single channel in the "United States" named "Web"
        And the store has a product "PHP Mug" priced at "$100.00"
        And there is a special "Disabled special"
        And it gives "10%" off on that product
        And this special starts tomorrow
        And this special was disabled
        And specials was reassigned

    Scenario: Initially product have same price as far as special starts tomorrow
        Then its price still should be "$100.00"

    Scenario: Accidentally disabled special should become enabled and applied to product
        And this special has started yesterday
        When I run check active CLI command
        Then the command should finish successfully
        And I should see output "Special 'disabled_special' was accidentally disabled"
        And I should see output "Special 'disabled_special' was enabled and recalculated"
        And its price should become "$90.00"
