# SetonoSyliusBulkSpecialsPlugin

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE)
[![Build Status][ico-travis]][link-travis]
[![Quality Score][ico-code-quality]][link-code-quality]

## Install

### Add plugin to composer.json

```bash
composer require setono/sylius-bulk-specials-plugin
```

### Register plugin at AppKernel.php

```php
# app/AppKernel.php

final class AppKernel extends Kernel
{
    public function registerBundles(): array
    {
        // Its important to instantiate SetonoSyliusBulkSpecialsPlugin
        // before calling parent::registerBundles()
        return array_merge([
            new \Setono\SyliusBulkSpecialsPlugin\SetonoSyliusBulkSpecialsPlugin(),
        ], parent::registerBundles(), [
            // ...
        ]);
    }
}
```

**Note**, that we MUST instantiate `SetonoSyliusBulkSpecialsPlugin` 
BEFORE `SyliusGridBundle` (which instantiates at `parent::registerBundles()`). 
Otherwise you'll see exception like this:

```bash
You have requested a non-existent parameter "setono_sylius_bulk_specials.model.special.class".  
```

### Add routing

```yaml
# app/config/routing.yml
setono_sylius_bulk_specials_admin:
    resource: "@SetonoSyliusBulkSpecialsPlugin/Resources/config/admin_routing.yml"
    prefix: /admin
```

### Configure plugin (optional)

```yaml
# app/config/config.yml
setono_sylius_bulk_specials:
    
```

### Update your schema (for existing project)

```bash
bin/console doctrine:schema:update --force
```

# (Manually) Test plugin

- Run application:
  (by default application have default config at `dev` environment
  and example config from `Configure plugin` step at `prod` environment)
  
    ```bash
    SYMFONY_ENV=dev
    cd tests/Application && \
        yarn install && \
        yarn run gulp && \
        bin/console assets:install web -e $SYMFONY_ENV && \
        bin/console doctrine:database:create -e $SYMFONY_ENV && \
        bin/console doctrine:schema:create -e $SYMFONY_ENV && \
        bin/console sylius:fixtures:load -e $SYMFONY_ENV && \
        bin/console server:run -d web -e $SYMFONY_ENV
    ```

- Log in at `http://localhost:8000/admin`
  with Sylius demo credentials:
  
  ```
  Login: sylius@example.com
  Password: sylius 
  ```

- ...

- See how much that item was ordered (or even added to cart depending on config)

[ico-version]: https://img.shields.io/packagist/v/setono/sylius-bulk-specials-plugin.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/Setono/SyliusBulkSpecialsPlugin/master.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/Setono/SyliusBulkSpecialsPlugin.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/setono/sylius-bulk-specials-plugin
[link-travis]: https://travis-ci.org/Setono/SyliusBulkSpecialsPlugin
[link-code-quality]: https://scrutinizer-ci.com/g/Setono/SyliusBulkSpecialsPlugin
