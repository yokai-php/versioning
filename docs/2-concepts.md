Concepts
--------


### Analyze models to analyze

Whenever you are about to create/update/delete something, you should trigger an analyze ot these objects,
so the library can decide whether or not there something to do with each of these.

> **note** deciding whether or not an object is under versioning is the job of the 
[update guesser components](components/update-guesser.md)

When to ask this library is up to you. You must call explicitly the library to analyze your objects.
Then, for each objects that you considered as created/updated/deleted, 
you must call the [VersionBuilder](../src/VersionBuilder.php).

> **integration** when using `doctrine/orm` [integration](integration/doctrine-orm.md) 
you do not need to trigger the analyze explicitly : a listener is doing it for you.


### Find current version of model

The first to do is to find whether or not there was a previous version for your object.
The [verson storage](components/version-storage.md) will be called to fetch it.


### Take a snapshot

Then, the [snapshot taker](components/snapshot-taker.md) will be called to take a snapshot of your object.

A snapshot is nothing more than a normalized representation of your object, ie : an array.

> **integration** did you noticed that we talked about normalization here ? 
You may use the `symfony/serializer` [integration](integration/symfony-serializer.md) to take a these snapshots.


### Determine changeset

With these information, the [ChangesetBuilder](../src/ChangesetBuilder.php) will be called with 2 snapshots to compare :
the previous that was fetched (or an empty array), the new one that was just fetched.

The snapshot will be an array, that look like to something like : 

```json
{
  "name": {"old": "Spoon", "new": "Very interesting spoon"},
  "price": {"old": 0.29, "new": 0.30}
}
```


### Gather contextual information

Before creating the version object, the [VersionBuilder](../src/VersionBuilder.php) 
will ask some contextual information (stored in [Context](../src/Context.php)).

There is three things we can extract from it :

* An entry point.
  It is a `string` that represent the place on which the changes was performed.
* Some parameters.
  It is an `array` that represent the parameters of the endpoint.
* An author. 
  It is an `object`, instance of [VersionableAuthorInterface](../src/VersionableAuthorInterface.php)
  that represent the one that introduced these changes.


> **integration** when using `symfony/event-dispatcher` [integration](integration/symfony-event-dispatcher.md),
the entry point and parameters may be filled using `route` and `route params` in http context, 
or with `command name` and `command arguments` plus `command options` in console context.
The author may be filled using authenticated user if available (and if it implements the interface).


### Create a version object

At this point, we have all required information to create a new version for our object.
The [version factory](components/version-factory.md) will be asked to create it.

Then, the [VersionBuilder](../src/VersionBuilder.php) will return the object version.

> **note** the version changeset may be empty, it is up to decide what to do with it.


### Store new version of model

The [VersionBuilder](../src/VersionBuilder.php) just built a version for your object(s), but it is not stored yet.
The [verson storage](components/version-storage.md) should be called to store the built version(s).

> **integration** when using `doctrine/orm` [integration](integration/doctrine-orm.md) 
you do not need to store the version by yourself : a listener is doing it for you.


---

« [Install](1-install.md) • [Usage](3-usage.md) »
