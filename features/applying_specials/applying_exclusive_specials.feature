@setono_sylius_bulk_discount_applying_specials
Feature: Receiving discount from most prioritized exclusive special
    As an Administrator
    I want exclusive specials applied in prioritized order

    Background:
        Given the store operates on a single channel in "United States"
        And the store has "Mugs" taxonomy
        And the store has a product "1L Mug" priced at "$100.00"
        And this product belongs to "Mugs"

    @ui
    Scenario: Receiving exclusive product discount from special with greater priority
        # This special shouldn't be applied
        Given there is a special "50% off for mugs"
        And it gives "50%" off on a "1L Mug" product
        # And this one - should
        And there is an exclusive special "10% off for 1L mugs" with priority 1
        And it gives "10%" off on a "1L Mug" product
        And there is an exclusive special "20% off for 1L mugs" with priority 2
        And it gives "20%" off on a "1L Mug" product
        When I reassign specials
        Then its price should become "$80.00"

    @ui
    Scenario: Receiving exclusive taxonomy discount from special with greater priority
        # This special shouldn't be applied
        Given there is a special "50% off for mugs"
        And it gives "50%" off on every product classified as "Mugs"
        # And this one - should
        And there is an exclusive special "10% off for 1L mugs" with priority 1
        And it gives "10%" off on every product classified as "Mugs"
        And there is an exclusive special "20% off for 1L mugs" with priority 2
        And it gives "30%" off on every product classified as "Mugs"
        When I reassign specials
        Then its price should become "$70.00"
