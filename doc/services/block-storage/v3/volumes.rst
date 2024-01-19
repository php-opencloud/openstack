Volumes
=======

A volume is a detachable block storage device similar to a USB hard drive. You can attach a volume to an instance, and
if the volume is of an appropriate volume type, a volume can be attached to multiple instances.

.. osdoc:: https://docs.openstack.org/api-ref/block-storage/v3/#volumes-volumes

.. |models| replace:: volumes

.. include:: /common/service.rst

Create
------

The only attributes that are required when creating a volume are a size in GiB. The simplest example
would therefore be this:

.. sample:: BlockStorage/v3/volumes/create.php

You can further configure your new volume, however, by following the below sections, which instruct you how to add
specific functionality.

Create from image
~~~~~~~~~~~~~~~~~

.. sample:: BlockStorage/v3/volumes/create_from_image.php

Create from snapshot
~~~~~~~~~~~~~~~~~~~~

.. sample:: BlockStorage/v3/volumes/create_from_snapshot.php

Create from volume
~~~~~~~~~~~~~~~~~~

.. sample:: BlockStorage/v3/volumes/create_from_volume.php


Read
----

.. sample:: BlockStorage/v3/volumes/read.php


Update
------

The first step when updating a volume is modifying the attributes you want updated. Only a volume's name
and description can be edited.

.. sample:: BlockStorage/v3/volumes/update.php

Delete
------

To permanently delete a volume:

.. sample:: BlockStorage/v3/volumes/delete.php

List
----

.. sample:: BlockStorage/v3/volumes/list.php

Each iteration will return a php:class:`Volume` instance <OpenStack/BlockStorage/v2/Models/Volume.html>.

.. include:: /common/generators.rst

Detailed information
~~~~~~~~~~~~~~~~~~~~

By default, only the ``id``, ``links`` and ``name`` attributes are returned. To return *all* information
for a flavor, you must enable detailed information:

.. sample:: BlockStorage/v3/volumes/list_detail.php
