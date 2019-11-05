# Sylius Bulk Discount Plugin

[![Latest Version][ico-version]][link-packagist]
[![Latest Unstable Version][ico-unstable-version]][link-packagist]
[![Software License][ico-license]](LICENSE)
[![Build Status][ico-github-actions]][link-github-actions]
[![Quality Score][ico-code-quality]][link-code-quality]

Plugin for Sylius to define permanent or time-limited discounts for products and automatically update prices.

Menu:

![Screenshot showing admin menu](docs/admin-menu.png)

Specials admin page:

![Screenshot showing specials admin page](docs/admin-specials.png)

Products admin page actions:

![Screenshot showing products admin actions](docs/admin-products-actions.png)

## Install

### Add plugin to composer.json

```bash
composer require setono/sylius-bulk-discount-plugin
```

### Register plugin

```php
<?php
# config/bundles.php

return [
    // ...
    Setono\SyliusBulkDiscountPlugin\SetonoSyliusBulkDiscountPlugin::class => ['all' => true],
    Sylius\Bundle\GridBundle\SyliusGridBundle::class => ['all' => true],
    // ...
];

```

**Note**, that we MUST define `SetonoSyliusBulkDiscountPlugin` BEFORE `SyliusGridBundle`.
Otherwise you'll see exception like this:

```bash
You have requested a non-existent parameter "setono_sylius_bulk_discount.model.discount.class".  
```

### Add config

```yaml
# config/packages/_sylius.yaml
imports:
    - { resource: "@SetonoSyliusBulkDiscountPlugin/Resources/config/app/config.yaml" }
```

### Add routing

```yaml
# config/routes.yaml
setono_sylius_bulk_discount_admin:
    resource: "@SetonoSyliusBulkDiscountPlugin/Resources/config/admin_routing.yaml"
    prefix: /admin
```

### Extend `Product` model and `ProductRepository`

(see [tests/Application](tests/Application) for more details how to configure)

* Override config

    ```yaml
    # app/config/config.yml
    sylius_product:
        resources:
            product:
                classes:
                    model: AppBundle\Model\Product
                    repository: AppBundle\Doctrine\ORM\ProductRepository
    ```

* Override model

    ```php
    <?php
    
    declare(strict_types=1);
    
    namespace AppBundle\Model;
    
    use Setono\SyliusBulkDiscountPlugin\Model\ProductInterface;
    use Setono\SyliusBulkDiscountPlugin\Model\SpecialSubjectTrait;
    use Sylius\Component\Core\Model\Product as BaseProduct;
    
    /**
     * Class Product
     */
    class Product extends BaseProduct implements ProductInterface
    {
        use SpecialSubjectTrait {
            SpecialSubjectTrait::__construct as private __specialSubjectTraitConstruct;
        }
    
        public function __construct()
        {
            $this->__specialSubjectTraitConstruct();
    
            parent::__construct();
        }
    }
    ```
    
* Override mapping

    ```xml
    <?xml version="1.0" encoding="UTF-8"?>
    
    <doctrine-mapping xmlns="http://doctrine-project.org/schemas/orm/doctrine-mapping"
                      xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                      xsi:schemaLocation="http://doctrine-project.org/schemas/orm/doctrine-mapping
                                          http://doctrine-project.org/schemas/orm/doctrine-mapping.xsd">
    
        <mapped-superclass name="AppBundle\Model\Product">
            <many-to-many field="specials" target-entity="Setono\SyliusBulkDiscountPlugin\Model\SpecialInterface">
                <cascade>
                    <cascade-persist />
                </cascade>
                <order-by>
                    <order-by-field name="priority" direction="DESC" />
                </order-by>
                <join-table name="setono_sylius_bulk_discount_products">
                    <join-columns>
                        <join-column name="special_id" referenced-column-name="id" nullable="false" on-delete="CASCADE" />
                    </join-columns>
                    <inverse-join-columns>
                        <join-column name="channel_id" referenced-column-name="id" nullable="false" on-delete="CASCADE" />
                    </inverse-join-columns>
                </join-table>
            </many-to-many>
        </mapped-superclass>
    
    </doctrine-mapping>
    
    ```

* Override repository

    ```php
    <?php
    # Doctrine/ORM/ProductRepository.php
    
    declare(strict_types=1);
    
    namespace AppBundle\Doctrine\ORM;
    
    use Setono\SyliusBulkDiscountPlugin\Doctrine\ORM\ProductRepositoryTrait;
    use Setono\SyliusBulkDiscountPlugin\Doctrine\ORM\ProductRepositoryInterface;
    use Setono\SyliusBulkDiscountPlugin\Special\QueryBuilder\Rule\RuleQueryBuilderAwareInterface;
    use Sylius\Bundle\CoreBundle\Doctrine\ORM\ProductRepository as BaseProductRepository;
    
    /**
     * Class ProductRepository
     */
    class ProductRepository extends BaseProductRepository
        implements ProductRepositoryInterface, RuleQueryBuilderAwareInterface
    {
        use ProductRepositoryTrait;
    }
    ``` 

* Override `ProductRepository` service definition as it shown at 
  [tests/Application/src/AppBundle/Resources/config/services.xml](tests/Application/src/AppBundle/Resources/config/services.xml).

  ```xml
    <service id="sylius.repository.product"
             class="%sylius.repository.product.class%">
        <factory service="doctrine.orm.default_entity_manager" method="getRepository" />
        <argument>%sylius.model.product.class%</argument>
        <call method="setRuleQueryBuilder">
            <argument type="service" id="setono_sylius_bulk_discount.registry.special_rule_query_builder" />
        </call>
    </service>
  ```

  ```yaml
     sylius.repository.product:
         class: "%sylius.repository.product.class%"
         factory: ["@doctrine.orm.default_entity_manager", "getRepository"]
         arguments:
           - "%sylius.model.product.class%"
         calls:
           - ["setRuleQueryBuilder", ["@setono_sylius_bulk_discount.registry.special_rule_query_builder"]]
  ```

### Update your schema

Create migration file:

```bash
$ php bin/console doctrine:migrations:diff
```

If you have existing discounted products you should append this line to the `up` method in the migration file:
```php
<?php
namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20191028134956 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // The generated SQL will be here
        // ...
        
        // append this line
        $this->addSql('UPDATE sylius_channel_pricing SET manually_discounted = 1 WHERE original_price IS NOT NULL AND price != original_price');
    }

    public function down(Schema $schema) : void
    {
        // ...
    }
}
```

Execute migration file:
```bash
$ php bin/console doctrine:migrations:migrate
```

### Install assets

```bash
bin/console sylius:install:assets
```

### Configure CRON to run next command every minute

```bash
bin/console setono:sylius-bulk-specials:check-active
```

# Contribution

## Installation

To automatically execute installation steps, load fixtures 
and run server with just one command, run:

```bash
# Optional step, if 5 mins enough for webserver to try
# @see https://getcomposer.org/doc/06-config.md#process-timeout
composer config --global process-timeout 0

composer try
```

or follow next steps manually:

* Initialize:

    ```bash
    SYMFONY_ENV=test
    (cd tests/Application && yarn install) && \
        (cd tests/Application && yarn build) && \
        (cd tests/Application && bin/console assets:install public -e $SYMFONY_ENV) && \
        (cd tests/Application && bin/console doctrine:database:create -e $SYMFONY_ENV) && \
        (cd tests/Application && bin/console doctrine:schema:create -e $SYMFONY_ENV)
    ```

* If you want to manually play with plugin test app, run:

    ```bash
    SYMFONY_ENV=test
    (cd tests/Application && bin/console sylius:fixtures:load --no-interaction -e $SYMFONY_ENV && \
        (cd tests/Application && bin/console server:run -d public -e $SYMFONY_ENV)
    ```

## Running plugin tests

  - PHPSpec

    ```bash
    $ composer phpspec
    ```

  - Behat (non-JS scenarios)

    ```bash
    $ composer behat
    ```

  - All tests (phpspec & behat)

    ```bash
    $ composer test
    ```

  - Behat (JS scenarios)
 
    1. Download [Chromedriver](https://sites.google.com/a/chromium.org/chromedriver/)
    
    2. Download [Selenium Standalone Server](https://www.seleniumhq.org/download/).
    
    2. Run Selenium server with previously downloaded Chromedriver:
    
        ```bash
        $ java -Dwebdriver.chrome.driver=chromedriver -jar selenium-server-standalone.jar
        ```
        
    3. Run test application's webserver on `localhost:8080`:
    
        ```bash
        $ (cd tests/Application && bin/console server:run localhost:8080 -d public -e test)
        ```
    
    4. Run Behat:
    
        ```bash
        $ vendor/bin/behat --tags="@javascript"

[ico-version]: https://poser.pugx.org/setono/sylius-bulk-discount-plugin/v/stable
[ico-unstable-version]: https://poser.pugx.org/setono/sylius-bulk-discount-plugin/v/unstable
[ico-license]: https://poser.pugx.org/setono/sylius-bulk-discount-plugin/license
[ico-github-actions]: https://github.com/Setono/SyliusRedirectPlugin/workflows/CI/badge.svg
[ico-code-quality]: https://img.shields.io/scrutinizer/g/Setono/SyliusBulkDiscountPlugin.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/setono/sylius-bulk-discount-plugin
[link-github-actions]: https://github.com/Setono/SyliusRedirectPlugin/actions
[link-code-quality]: https://scrutinizer-ci.com/g/Setono/SyliusBulkDiscountPlugin
