Initialize
----------

If you installed this library in a project that already has some versionable resource models.
You may want to initialize the first version of these objects in the version storage.

This is the responsibility of the [Initializer](../src/Initialize/Initializer.php).

The initializer will trigger the version analyze process for each objects 
fetched by the [version finder](components/object-finder.md).

> **note** the [Symfony Console component](integration/symfony-console.md) integration has a command to trigger it.


---

« [Usage](3-usage.md) • [Purge](5-purge.md) »
