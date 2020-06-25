Symfony serializer integration
------------------------------

> **integration** when using `symfony/framework-bundle` [integration](../integration/symfony-framework.md) 
services are registered for you according to the configuration you provided.


### Taking snapshots

If you wish to take objects snapshots using the serializer component.
Use this integration.

```php
<?php

/** @var \Symfony\Component\Serializer\Serializer $serializer */

use Yokai\Versioning\Bridge\Symfony\Serializer\NormalizerSnapshotTaker;
use Yokai\Versioning\ChangesetBuilder;
use Yokai\Versioning\VersionBuilder;

$snapshotTaker = new NormalizerSnapshotTaker($serializer);

// build other dependencies

$versionBuilder = new VersionBuilder(
    $typesConfig,
    $versionStorage,
    $snapshotTaker,
    new ChangesetBuilder(),
    $context,
    $versionFactory
);

// analyze and store versions
```


### Doctrine entity normalizer



```php
<?php

/** @var \Doctrine\Common\Persistence\ManagerRegistry $managerRegistry */
/** @var \Symfony\Component\PropertyAccess\PropertyAccessorInterface $propertyAccessor */

use Symfony\Component\Serializer\Serializer;
use Yokai\Versioning\Bridge\Symfony\Serializer\Normalizer\DoctrineResourceNormalizer;
use Yokai\Versioning\Bridge\Symfony\Serializer\NormalizerSnapshotTaker;

$snapshotTaker = new NormalizerSnapshotTaker(
    new Serializer([new DoctrineResourceNormalizer($managerRegistry, $propertyAccessor)])
);

$snapshotTaker->take($resource);
```


---

« [README](../../README.md)
