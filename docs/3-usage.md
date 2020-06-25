Usage
-----

The following example assume that you (as a developer) provided an implementation for each of the following :

* `MyVersionFactory` : a [version factory](components/version-factory.md)
* `MyVersionStorage` : a [version storage](components/version-storage.md)
* `MySnapshotTaker` : a [snapshot taker](components/snapshot-taker.md)

> **note** keep in mind that the library already provide at least one implementation for each.

Additionally, the example assume that the following models exists and are :

* `Acme\Model\BlogPost` : a valid [versionable resource](components/versionable-resource.md)
* `Acme\Model\User` : a valid [versionable author](components/versionable-author.md)


```php
<?php

use Yokai\Versioning\ChangesetBuilder;
use Yokai\Versioning\TypesConfig;
use Yokai\Versioning\Context;
use Yokai\Versioning\VersionBuilder;
use Yokai\Versioning\UpdateGuesser\ChainUpdateGuesser;
use Yokai\Versioning\UpdateGuesser\UpdateGuesserInterface;
use Yokai\Versioning\UpdateGuesser\VersionableChildUpdateGuesser;
use Yokai\Versioning\UpdateGuesser\VersionableUpdateGuesser;

/** @var \Yokai\Versioning\VersionFactoryInterface $versionFactory */
$versionFactory = new MyVersionFactory();
/** @var \Yokai\Versioning\Storage\VersionStorageInterface $versionStorage */
$versionStorage = new MyVersionStorage();
/** @var \Yokai\Versioning\SnapshotTakerInterface $snapshotTaker */
$snapshotTaker = new MySnapshotTaker();

$typesConfig = new TypesConfig(
    ['user' => 'Acme\Model\BlogPost'],
    ['blog-post' => 'Acme\Model\User']
);

$context = new Context();
$context->setEntryPoint($theAction);
$context->setParameters($theActionParameters);
$context->setAuthor($theUserThatTriggeredTheChanges);

$updateGuesser = new ChainUpdateGuesser([new VersionableUpdateGuesser(), new VersionableChildUpdateGuesser()]);

$versionBuilder = new VersionBuilder(
    $typesConfig, 
    $versionStorage, 
    $snapshotTaker, 
    new ChangesetBuilder(), 
    $context, 
    $versionFactory
);

$versions = [];

$analyzeHash = [
    UpdateGuesserInterface::ACTION_INSERT => $createdObjects,
    UpdateGuesserInterface::ACTION_UPDATE => $updatedObjects,
    UpdateGuesserInterface::ACTION_DELETE => $deletedObjects,
];

foreach ($analyzeHash as $action => $objects) {
    foreach ($objects as $object) {
        foreach ($updateGuesser->guessUpdates($object, $action) as $versionable) {
            $version = $versionBuilder->build($object);
            
            if (count($version->getChangeSet()) === 0) {
                continue;
            }
            
            $versions[] = $version;
        }
    }
}

$versionStorage->store($versions);
```

What is happening here ?

* First, an instance of each of your components are created.
* An instance of [TypesConfig](../src/TypesConfig.php) is created.
  It will store mapping of storage identifier for each versionable resource / author.
* An instance of [Context](../src/Context.php) is created.
  It will store the context of the triggered changes.
* The [Context](../src/Context.php) is filled using some information (optional).
* An [update guesser](components/update-guesser.md) is created.
  It will find what versionable resources are tracked for an object.
* An instance of [VersionBuilder](../src/VersionBuilder.php), with all its dependencies is created.
  It will build a version for a each versionable objects.
* An analyze will be triggered for each inserted/updated/deleted objects, 3 steps :
  * Find versionables resources associated, using the update guesser.
  * Build a version for each of the versionable resources.
  * Add the version object to an array if the changeset is not empty.
* The version storage is called with the list of built version to store through persistence.


---

« [Concepts](2-concepts.md) • [Initialize](4-initialize.md) »
