Account
=======

Account represents the top-level of the hierarchy. Your service provider creates your account and you own all resources
in that account. The account defines a namespace for containers. In the OpenStack environment, account is synonymous
with a project or tenant.

.. osdoc:: https://docs.openstack.org/api-ref/object-store/#accounts

.. |models| replace:: accounts

.. include:: /common/service.rst

Read
----

To work with an Object Store account, you must first retrieve an account object like so:

.. sample:: ObjectStore/v1/account/read.php

Get account metadata
--------------------

.. sample:: ObjectStore/v1/account/get_metadata.php

Replace all metadata with new values
------------------------------------

In order to replace all existing metadata with a set of new values, you can use this operation. Any existing metadata
items which not specified in the new set will be removed. For example, say an account has the following metadata
already set:

::

    Foo: value1
    Bar: value2

and you *reset* the metadata with these values:

::

    Foo: value4
    Baz: value3

the metadata of the account will now be:

::

    Foo: value4
    Baz: value3

To merge metadata, you must run:

.. sample:: ObjectStore/v1/account/reset_metadata.php
.. refdoc:: OpenStack/ObjectStore/v1/Models/Account.html#method_resetMetadata

Merge new metadata values with existing
---------------------------------------

In order to merge a set of new metadata values with the existing metadata set, you can use this operation. Any existing
metadata items which are not specified in the new set will be preserved. For example, say an account has the following
metadata already set:

::

    Foo: value1
    Bar: value2

and you merge them with these values:

::

    Foo: value4
    Baz: value3

the metadata of the account will now be:

::

    Foo: value4
    Bar: value2
    Baz: value3

To reset metadata, you must run:

.. sample:: ObjectStore/v1/account/merge_metadata.php
