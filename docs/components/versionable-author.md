Versionable Author
------------------

In the [version](version.md) object, a versionable author is identified by a type and an id.

The [VersionableAuthorInterface](../../src/VersionableAuthorInterface.php) 
is the interface for all authors may introduce changes.
The interface has a single method that must return the object id, **as a string**.

The object type is stored in the [TypesConfig](../../src/TypesConfig.php) authors map.

For example :

```php
<?php

use Yokai\Versioning\VersionableAuthorInterface;

class User implements VersionableAuthorInterface
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    public function getVersionableId(): string
    {
        return (string)$this->id;
    }
}
```

```php
<?php

new Yokai\Versioning\TypesConfig(['user' => 'User'], []);
```

> **note** types config versionable map config is a hash array with storage type as key and model FQCN as value.


---

« [README](../../README.md)
