@setono_sylius_bulk_specials_applying_specials
Feature: Applying valid specials even if they accidentally disabled
    As an Administrator
    I want valid specials applied even if they accidentally disabled

    Background:
        Given the store operates on a single channel in the "United States" named "Web"
        And the store has a product "PHP T-Shirt" priced at "$100.00"
        And there is a special "Holiday special"
        And it gives "10%" off on that product
        And this special was disabled

    @ui
    Scenario: Not receiving discount when special is disabled at all
        When I reassign specials
        Then its price should become "$90.00"
        And its original price should become "$100.00"
