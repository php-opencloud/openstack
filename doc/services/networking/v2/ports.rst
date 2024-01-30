Ports
=====

A port is a connection point for attaching a single device, such as the NIC of a virtual server, to a virtual network.
The port also describes the associated network configuration, such as the MAC and IP addresses to be used on that port.

.. osdoc:: https://docs.openstack.org/api-ref/network/v2/index.html#ports

.. |models| replace:: ports

.. include:: /common/service.rst

Create
------

.. sample:: Networking/v2/ports/create.php

Batch
~~~~~

To create multiple ports in a single request, use the following code:

.. sample:: Networking/v2/ports/create_batch.php

Read
----

.. sample:: Networking/v2/ports/read.php

Update
------

.. sample:: Networking/v2/ports/update.php

Delete
------

.. sample:: Networking/v2/ports/delete.php
