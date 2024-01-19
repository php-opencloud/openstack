Volume Types
============

Volume type is a group of volume policies. They can be used to specify which driver must be used on volume creation.

.. osdoc:: https://docs.openstack.org/api-ref/block-storage/v3/#volumes-volumes

.. |models| replace:: volume types

.. include:: /common/service.rst

Create
------

The only attributes that are required when creating a volume are a name.

.. sample:: BlockStorage/v3/volume_types/create.php


Read
----

.. sample:: BlockStorage/v3/volume_types/read.php


Update
------

.. sample:: BlockStorage/v3/volume_types/update.php


Delete
------

To permanently delete a volume type:

.. sample:: BlockStorage/v3/volume_types/delete.php

List
----
.. sample:: BlockStorage/v3/volume_types/list.php

Each iteration will return a :php:class:`VolumeType` instance <OpenStack/BlockStorage/v2/Models/VolumeType.html>.

.. include:: /common/generators.rst
