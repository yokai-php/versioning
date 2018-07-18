Symfony security integration
----------------------------

> **integration** when using `symfony/framework-bundle` [integration](../integration/symfony-framework.md) 
services are registered for you according to the configuration you provided.

> **note** this integration is highly coupled to the [event dispatcher](symfony-event-dispatcher.md) integration.

If you wish to extract the version author from security token.

When creating the [InitContextListener](../../src/Bridge/Symfony/EventDispatcher/EventListener/InitContextListener.php)
you need to provide an instance of token storage.

Then your security user needs to implements the 
[VersionAuthorInterface](../../src/VersionableAuthorInterface.php) interface.

```php
<?php

use Symfony\Component\Security\Core\User\UserInterface;
use Yokai\Versioning\VersionableAuthorInterface;

class User implements UserInterface, VersionableAuthorInterface
{
    /**
     * @var int
     */
    public $id;

    /* ... */

    public function getVersionableId(): string
    {
        return (string)$this->id;
    }
}
```


---

« [README](../../README.md)
