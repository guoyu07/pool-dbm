Pool-DBM (Pool Database Manager)
=======

**Requires** at least *PHP 5.3.3* with Doctrine 2 library. Compatible PHP 5.4 too.

[![Build Status](https://travis-ci.org/pokap/pool-dbm.png?branch=master)](https://travis-ci.org/pokap/pool-dbm)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/dc654b6f-aaca-4b69-aeae-5774d74e3a36/mini.png)](https://insight.sensiolabs.com/projects/dc654b6f-aaca-4b69-aeae-5774d74e3a36)
[![Latest Stable Version](https://poser.pugx.org/pokap/pool-dbm/v/stable.png)](https://packagist.org/packages/pokap/pool-dbm)

PoolDBM package support doctrine common. You should know that this is an additional layer. But for to limit
potential performance lowering, the mapping does not use reflection.
It only dispatch features to doctrine managers.

Compatibility
-------------

The `composer.json` file in each branch indicates Doctrine2 compatibility.
Additionally, several tags are available:

 * `1.2.x` for Doctrine 2.4
 * `1.1.x` for Doctrine 2.3
 * `1.0.x` for Doctrine 2.2

Next Improvement
-------------

Optimize request doctrine with relation. And adding tests with real dbm.

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


[![Bitdeli Badge](https://d2weczhvl823v0.cloudfront.net/pokap/pool-dbm/trend.png)](https://bitdeli.com/free "Bitdeli Badge")

