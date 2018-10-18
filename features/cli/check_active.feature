@special_commands @cli
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

    Scenario: Accidentally enabled special should be disabled
        Given there is a special "Enabled special"
        And this special was enabled
        And this special has already expired
        When I run check active CLI command
        Then the command should finish successfully
        And I should see output "Special 'enabled_special' was accidentally enabled"
        And I should see output "Special 'enabled_special' was disabled and recalculated"

    Scenario: Accidentally disabled special should be enabled
        Given there is a special "Disabled special"
        And this special was disabled
        And this special has started yesterday
        When I run check active CLI command
        Then the command should finish successfully
        And I should see output "Special 'disabled_special' was accidentally disabled"
        And I should see output "Special 'disabled_special' was enabled and recalculated"
