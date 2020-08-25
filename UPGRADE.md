# UPGRADE NOTES

## From 0.1 to 0.2

1. Update database schema

    ```bash
    bin/console doctrine:migrations:diff
    bin/console doctrine:migrations:migrate
    ```

    And make sure you have something like this in it:

    ```
    ALTER TABLE setono_sylius_catalog_promotion__promotion CHANGE discount discount NUMERIC(10, 5) DEFAULT '0' NOT NULL;
    ```
