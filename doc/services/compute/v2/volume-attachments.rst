Volume Attachments
==================

Nova allows you to attach a volume to a server on the fly. This model represents a point of attachment between a server and a volume.

.. osdoc:: https://docs.openstack.org/api-ref/compute/#servers-with-volume-attachments-servers-os-volume-attachments

.. |models| replace:: volume attachments

.. include:: /common/service.rst

.. warning::

    The server must be fully started before you can attach a volume to it. Just because the server is in the ``ACTIVE``
    state does not mean that it is ready to accept a volume attachment. See https://bugs.launchpad.net/nova/+bug/1960346
    and https://bugs.launchpad.net/nova/+bug/1998148 for more information.

Create
------

To attach a volume to a server, you need to know the server ID and the volume ID.

.. sample:: Compute/v2/volume_attachments/create.php


Delete
------

To detach a volume from a server, you need to know the server ID and the volume ID.

.. sample:: Compute/v2/volume_attachments/delete.php

List
----

.. sample:: Compute/v2/volume_attachments/list.php

.. include:: /common/generators.rst
