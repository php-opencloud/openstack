Snapshots
=========

A snapshot is read-only point in time copy of a volume. The snapshot can be created from a volume that is currently in use
or in an available state. The snapshot can then be used to create a new volume.

.. osdoc:: https://docs.openstack.org/api-ref/block-storage/v3/#volume-snapshots-snapshots

.. |models| replace:: snapshots

.. include:: /common/service.rst

List
----

.. sample:: BlockStorage/v3/snapshots/list.php

Each iteration will return a php:class:`Snapshot` instance <OpenStack/BlockStorage/v2/Models/Snapshot.html>.

.. include:: /common/generators.rst

List sorted
~~~~~~~~~~~

Possible values for sort_key are:

* display_name

Possible values for sort_dir are:

* asc
* desc

.. sample:: BlockStorage/v3/snapshots/list_sorted.php