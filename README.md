Tagwalk API Client Bundle
=========================

[![Build Status](https://travis-ci.org/tag-walk/tagwalk-api-client.svg?branch=master)](https://travis-ci.org/tag-walk/tagwalk-api-client)

### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
$ composer require tagwalk/tagwalk-api-client
```

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

### Step 2: Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles
in the `config/bundles.php` file of your project:

```php
<?php
// config/bundles.php
return [
    // ...
    Tagwalk\ApiClientBundle\TagwalkApiClientBundle::class => ['all' => true]
];
```

### Step 3: Load bundle routes (optionnal)

Edit your project file `routes.yaml` to include the routes from the bundle

```yaml
api_client_bundle:
    resource: "@TagwalkApiClientBundle/Controller/"
    type:     annotation
    prefix:   /api
```

### More

* [Configuration](Resources/doc/index.md)
