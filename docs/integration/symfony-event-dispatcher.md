Symfony event dispatcher integration
------------------------------------

This integration is about how to populate context using events.

It declare a listener that could subscribes to 2 events :

* `kernel.request` (from [symfony/http-kernel](https://symfony.com/doc/current/components/http_kernel.html#the-kernel-request-event))
* `console.command` (from [symfony/console](https://symfony.com/doc/current/components/console/events.html#the-consoleevents-command-event))

> **integration** for both events, you may use the additional [symfony security](symfony-security.md) integration 
to populate author from security.


### Subscribing to `kernel.request`

When subscribing to this event, context with be populated with :

* **entry point** : the `_route` attribute of the `Request` object
* **parameters** : the `_route_params` attribute of the `Request` object

```php
<?php

/** @var \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher */

use Symfony\Component\HttpKernel\KernelEvents;
use Yokai\Versioning\Bridge\Symfony\EventDispatcher\EventListener\InitContextListener;
use Yokai\Versioning\Context;

$context = new Context();
$listener = new InitContextListener($context);

$eventDispatcher->addListener(KernelEvents::REQUEST, [$listener, 'onRequest']);

$eventDispatcher->dispatch(KernelEvents::REQUEST, /* ...*/);
```


### Subscribing to `console.command`

When subscribing to this event, context with be populated with :

* **entry point** : the command name
* **parameters** : the command arguments & parameters

```php
<?php

/** @var \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher */

use Symfony\Component\Console\ConsoleEvents;
use Yokai\Versioning\Bridge\Symfony\EventDispatcher\EventListener\InitContextListener;
use Yokai\Versioning\Context;

$context = new Context();
$listener = new InitContextListener($context);

$eventDispatcher->addListener(ConsoleEvents::COMMAND, [$listener, 'onCommand']);

$eventDispatcher->dispatch(ConsoleEvents::COMMAND, /* ...*/);
```


---

« [README](../../README.md)
