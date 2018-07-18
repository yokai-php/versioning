Doctrine ORM integration
------------------------

> **integration** when using `symfony/framework-bundle` [integration](../integration/symfony-framework.md) 
services are registered for you according to the configuration you provided.


### Automatically analyze doctrine entities

If your application contains doctrine entities you whish to automatically analyze whenever 
doctrine is applying changes on it.
You can configure the listener that will do it for you :

```php
<?php

/** @var \Doctrine\Common\EventManager $eventManager */

use Yokai\Versioning\Bridge\Doctrine\ORM\EventListener\CreateVersionListener;

$eventManager->addEventSubscriber(
    new CreateVersionListener(
        $updateGuesser,
        $versionBuilder,
        $versionStorage
    )
);
```


### Version as an ORM entity

If you wish to store your versions using an entity.

```php
<?php

use Doctrine\ORM\Configuration;
use Doctrine\ORM\Mapping\Driver\SimplifiedXmlDriver;

$config = new Configuration();
$driver = new SimplifiedXmlDriver(
    ['/path/to/lib/src/Bridge/Doctrine/ORM/Metadata' => 'Yokai\\Versioning\\Bridge\\Doctrine\\ORM\\Entity']
);
$config->setMetadataDriverImpl($driver);
```

Then you will need to provide concrete implementations of :

* [version storage](../components/version-storage.md) : 
  [VersionEntityStorage](../../src/Bridge/Doctrine/ORM/Storage/VersionEntityStorage.php)
* [version factory](../components/version-factory.md) : 
  [VersionEntityFactory](../../src/Bridge/Doctrine/ORM/VersionEntityFactory.php)

```php
<?php

/** @var \Doctrine\Common\Persistence\ManagerRegistry $doctrineRegistry */

use Yokai\Versioning\Bridge\Doctrine\ORM\Storage\VersionEntityStorage;
use Yokai\Versioning\Bridge\Doctrine\ORM\VersionEntityFactory;
use Yokai\Versioning\ChangesetBuilder;
use Yokai\Versioning\VersionBuilder;

$versionFactory = new VersionEntityFactory();
$versionStorage = new VersionEntityStorage($doctrineRegistry);

// build other dependencies

$versionBuilder = new VersionBuilder(
    $typesConfig,
    $versionStorage,
    $snapshotTaker,
    new ChangesetBuilder(),
    $context,
    $versionFactory
);

// analyze

$versionStorage->store($versions);
```


### Resources ORM entities

If your versions are related to doctrine entities and you which to extract the entity from certain version.

```php
<?php

/** @var \Doctrine\Common\Persistence\ManagerRegistry $doctrineRegistry */
/** @var \Yokai\Versioning\TypesConfig $typesConfig */
/** @var \Yokai\Versioning\VersionInterface $version */

use Yokai\Versioning\Bridge\Doctrine\ORM\Storage\ResourceEntityStorage;
use Yokai\Versioning\Finder\ResourceFinder;
use Yokai\Versioning\Storage\ChainResourceStorage;

$finder = new ResourceFinder(
    new ChainResourceStorage([
        new ResourceEntityStorage($doctrineRegistry)
    ]),
    $typesConfig
);

$entity = $finder->findForVersion($version);
```


### Author ORM entities

If your versions are authored by doctrine entities and you which to extract the entity from certain version.

```php
<?php

/** @var \Doctrine\Common\Persistence\ManagerRegistry $doctrineRegistry */
/** @var \Yokai\Versioning\TypesConfig $typesConfig */
/** @var \Yokai\Versioning\VersionInterface $version */

use Yokai\Versioning\Bridge\Doctrine\ORM\Storage\AuthorEntityStorage;
use Yokai\Versioning\Finder\AuthorFinder;
use Yokai\Versioning\Storage\ChainAuthorStorage;

$finder = new AuthorFinder(
    new ChainAuthorStorage([
        new AuthorEntityStorage($doctrineRegistry)
    ]),
    $typesConfig
);

$entity = $finder->findForVersion($version);
```


### ORM Entity Initializer

If the objects you are analysing are ORM entities 
and you which to trigger version [initialization](../4-initialize.md) for these entities.
You can use the object finder provided by this integration.

```php
<?php

/** @var \Doctrine\Common\Persistence\ManagerRegistry $doctrineRegistry */

use Yokai\Versioning\Bridge\Doctrine\ORM\Initialize\EntityFinder;
use Yokai\Versioning\Initialize\ChainObjectFinder;
use Yokai\Versioning\Initialize\Initializer;

$initializer = new Initializer(
    new ChainObjectFinder([
        new EntityFinder($doctrineRegistry),
    ]),
    $updateGuesser,
    $versionBuilder,
    $versionStorage
);

$initializer->initialize('Acme\\Model');
```


### ORM Purge strategies

If you decided to store your versions as ORM entities and you which to [purge](../5-purge.md) these entities.
You can use the purge strategies provided by this integration.

```php
<?php

/** @var \Doctrine\Common\Persistence\ManagerRegistry $doctrineRegistry */

use Yokai\Versioning\Bridge\Doctrine\ORM\Purge\ObsoletePurger;
use Yokai\Versioning\Bridge\Doctrine\ORM\Purge\OldPurger;
use Yokai\Versioning\Purge\ChainPurger;

$purger = new ChainPurger([
    new ObsoletePurger($doctrineRegistry),
    new OldPurger($doctrineRegistry, '6 month')
]);

$purger->purge();
```

* [ObsoletePurger](../../src/Bridge/Doctrine/ORM/Purge/ObsoletePurger.php) : 
remove versions that are related to resources that do not exists anymore.
* [OldPurger](../../src/Bridge/Doctrine/ORM/Purge/OldPurger.php) : 
remove versions that are older than some date (see [date modifier](http://php.net/manual/en/datetime.modify.php)).


---

« [README](../../README.md)
