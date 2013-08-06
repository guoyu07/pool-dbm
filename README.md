Pool-DBM (Pool Database Manager)
=======

**Requires** at least *PHP 5.3.3* with Doctrine 2 library. Compatible PHP 5.4 too.

[![Build Status](https://travis-ci.org/pokap/pool-dbm.png?branch=1.0)](https://travis-ci.org/pokap/pool-dbm)

PoolDBM package support doctrine common. You should know that this is an additional layer. But for to limit
potential performance lowering, the mapping does not use reflection.
It only dispatch features to doctrine managers.

Compatibility
-------------

The `composer.json` file in each branch indicates Doctrine2 compatibility.
Additionally, several tags are available:

 * `1.0.x` for Doctrine 2.2

Next Improvement
-------------

Support of reference one and many to other multi-model.
With cascade persist, remove, etc.

Usage
-------------

The package has several class in debug mode, use these classes during development of your application.
Example `Pok\PoolDBM\ModelManagerDebug` check method parameter and class-metadata info.

Mapping:

``` xml

<multi-model model="MultiModel\User" repository-class="Repository\UserRepository">
    <model-reference manager="entity" field="dataId">
        <reference manager="document" field="id" reference-field="id" />

        <id-generator target-manager="document" />
    </model-reference>

    <model manager="entity" name="Entity\User" repository-method="findByIds">
        <field name="name" />
    </model>

    <model manager="document" name="Document\User">
        <field name="profileContent" />
    </model>

    <relation-one field="address" target-model="MultiModel\Address">
        <field-reference manager="document" field="addressUser" />
    </relation-one>

    <!-- compatible can to be empty for all managers, or defines several managers -->
    <relation-many field="posts" target-model="MultiModel\Post" compatible="entity,document" />
</multi-model>
```