Yokai Versioning
================

[![Build Status](https://travis-ci.org/yokai-php/versioning.svg?branch=master)](https://travis-ci.org/yokai-php/versioning)

todo badges

Introduction
------------

This library aims to provide storage agnostic mechanics to add versioning capabilities to any PHP application.


Documentation
-------------

Start reading the library documentation.

* [Installation](docs/1-install.md)
* [Concepts](docs/2-concepts.md) (**highly recommended**)
* [Usage](docs/3-usage.md)
* [Initialize](docs/4-initialize.md)
* [Purge](docs/5-purge.md)


Learn more about model classes to implement.

* [Version](docs/components/version.md)
* [Versionable Resource](docs/components/versionable-resource.md)
* [Versionable Author](docs/components/versionable-author.md)


Learn more about core components.

* [Author Storage](docs/components/author-storage.md)
* [Object finder](docs/components/object-finder.md)
* [Purger](docs/components/purger.md)
* [Snapshot Taker](docs/components/snapshot-taker.md)
* [Update Guesser](docs/components/update-guesser.md)
* [Version Factory](docs/components/version-factory.md)
* [Version Storage](docs/components/version-storage.md)


Or jump to integrations.

| Require                    | Purpose                                              | Documentation                                        |
| -------------------------- | ---------------------------------------------------- | ---------------------------------------------------- |
| `symfony/framework-bundle` | Framework integration                                | [here](docs/integration/symfony-framework.md)        |
| `symfony/event-dispatcher` | Initialize context using events                      | [here](docs/integration/symfony-event-dispatcher.md) |
| `symfony/serializer`       | Take snapshots using normalization                   | [here](docs/integration/symfony-serializer.md)       |
| `doctrine/orm`             | Store versions using ORM, track versionable entities | [here](docs/integration/doctrine-orm.md)             |
| `symfony/security`         | Initialize context authors using authenticated user  | [here](docs/integration/symfony-security.md)         |
| `symfony/console`          | Add command to purge/initialize versionable models   | [here](docs/integration/symfony-console.md)          |


If you did not find what you was looking for ? Have a look to the recipes.

* [Custom Author storage](docs/recipes/custom-author-storage.md)
* [Custom Object Finder](docs/recipes/custom-object-finder.md)
* [Custom Resource storage](docs/recipes/custom-resource-storage.md)
* [Custom Version storage](docs/recipes/custom-version-storage.md)


MIT License
-----------

License can be found [here](LICENSE).


Authors
-------

The library was originally created by [Yann Eugoné](https://github.com/yann-eugone).
See the list of [contributors](https://github.com/yokai-php/versioning/contributors).


---

Thank's to [Prestaconcept](https://github.com/prestaconcept) for supporting this library.
