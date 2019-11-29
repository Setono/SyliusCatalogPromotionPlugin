@setono_sylius_catalog_promotions_commands
Feature: Special CLI feature
    In order to enable/disable specials via CLI
    As a Developer
    I want expired specials to be disabled and related products prices to be recalculated

    Background:
        Given the store operates on a single channel in the "United States" named "Web"
        And the store has a product "PHP Mug" priced at "$100.00"
        And there is a special "Enabled special"
        And it gives "10%" off on that product
        And this special was enabled
        And this special has started yesterday
        And specials was reassigned

    Scenario: Initially product have discounted price as far as special already started
        Then its price should become "$90.00"

    Scenario: Accidentally enabled special should be disabled
        Given this special has already expired
        When I run check active CLI command
        Then the command should finish successfully
        And I should see output "Special 'enabled_special' was accidentally enabled"
        And I should see output "Special 'enabled_special' was disabled and recalculated"
        Then its price still should be "$100.00"
