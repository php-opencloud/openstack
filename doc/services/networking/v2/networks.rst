Networks
========

Network represents an isolated Layer-2 networking segment within the cloud. It can be shared across tenants,
or isolated to a single tenant.

.. osdoc:: https://docs.openstack.org/api-ref/network/v2/index.html#layer-2-networking

.. |models| replace:: networks

.. include:: /common/service.rst

Create
--------------

.. sample:: Networking/v2/networks/create.php

Batch
~~~~~

To create multiple networks in a single request, use the following code:

.. sample:: Networking/v2/networks/create_batch.php

Read
----

.. sample:: Networking/v2/networks/read.php

Update
------

.. sample:: Networking/v2/networks/update.php

Delete
------

.. sample:: Networking/v2/networks/delete.php
