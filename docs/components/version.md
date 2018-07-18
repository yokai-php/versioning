Version
-------

The [VersionInterface](../../src/VersionInterface.php) is the interface for version object.

A version is the object representing a single change for one of your object.

Regarding the interface, you may noticed that it is composed with the following : 

* a **resource type** and a **resource id** : identify the [versionable resource](versionable-resource.md) 
  to which the version is related
* a **version number** : an increment number that represent the number of revision for the same object
* a **snapshot** : a normalized [snapshot](snapshot-taker.md) of the object with that version changes
* a **changeset** : a changeset between previous and current snapshot
* a **date** : the date at which the version was created

And optionally :

* a **author type** and a **author id** : identify the [versionable author](versionable-author.md) 
  that introduce the changes
* a **context entry point** and a **context parameters** : the contextual information

> **note** each [version storage](version-storage.md) could implement its own version final class.


---

« [README](../../README.md)
