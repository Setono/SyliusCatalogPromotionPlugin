@setono_sylius_catalog_promotions_commands
Feature: Special CLI feature
    In order to enable/disable specials via CLI
    As a Developer
    I want to run CLI commands

    Background:
        Given the store operates on a single channel in the "United States" named "Web"

    Scenario: No accidentally enabled/disabled specials
        When I run check active CLI command
        Then the command should finish successfully
        And I should see output "Done"
