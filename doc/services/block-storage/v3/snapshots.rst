Snapshots
=========

List volumes
------------

.. sample:: BlockStorage/v3/snapshots/list.php
.. refdoc:: OpenStack/BlockStorage/v2/Service.html#method_listSnapshots

Each iteration will return a php:class:`Snapshot` instance <OpenStack/BlockStorage/v2/Models/Snapshot.html>.

.. include:: /common/generators.rst

List volumes sorted
~~~~~~~~~~~~~~~~~~~

Possible values for sort_key are:

* display_name

Possible values for sort_dir are:

* asc
* desc

.. sample:: BlockStorage/v3/snapshots/list_sorted.php