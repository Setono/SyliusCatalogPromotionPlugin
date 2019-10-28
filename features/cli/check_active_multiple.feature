@setono_sylius_bulk_discount_commands
Feature: Special CLI feature
    In order to enable/disable specials via CLI
    As a Developer
    I want to run CLI commands

    Background:
        Given the store operates on a single channel in the "United States" named "Web"
        And there is a special "Enabled special"
        And this special was enabled
        And this special has already expired
        And there is a special "Disabled special"
        And this special was disabled
        And this special has started yesterday

    Scenario: Accidentally enabled/disabled special should be disabled/enabled
        When I run check active CLI command
        Then the command should finish successfully
        And I should see output "Special 'enabled_special' was accidentally enabled"
        And I should see output "Special 'enabled_special' was disabled and recalculated"
        And Output shouldn't contain "Special 'enabled_special' was enabled"
        And I should see output "Special 'disabled_special' was accidentally disabled"
        And I should see output "Special 'disabled_special' was enabled and recalculated"
        And Output shouldn't contain "Special 'disabled_special' was disabled"
