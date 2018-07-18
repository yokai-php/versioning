Versionable Resource
--------------------

In the [version](version.md) object, a versionable resource is identified by a type and an id.


### The Resource interface

The [VersionableResourceInterface](../../src/VersionableResourceInterface.php) 
is the interface for all resources under versioning.
The interface has a single method that must return the object id, **as a string**.

The object type is stored in the [TypesConfig](../../src/TypesConfig.php) resources map.

For example :

```php
<?php

use Yokai\Versioning\VersionableResourceInterface;

class Product implements VersionableResourceInterface
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var float
     */
    public $price;

    public function getVersionableId(): string
    {
        return (string)$this->id;
    }
}
```

```php
<?php

new Yokai\Versioning\TypesConfig([], ['product' => 'Product']);
```

> **note** types config versionable map config is a hash array with storage type as key and model FQCN as value.


### The Resource Parent interface

```php
```


### The Resource Child interface

```php
```


---

« [README](../../README.md)
