Subnets
=======

A block of IP addresses and associated configuration state. This is also known as the native IPAM (IP Address Management)
provided by the networking service for both project and provider networks. Subnets are used to allocate IP addresses
when new ports are created on a network.

.. osdoc:: https://docs.openstack.org/api-ref/network/v2/index.html#subnets

.. |models| replace:: subnets

.. include:: /common/service.rst

Create
------

.. sample:: Networking/v2/subnets/create.php

With gateway IP
~~~~~~~~~~~~~~~

.. sample:: Networking/v2/subnets/create_with_gateway_ip.php

With host routes
~~~~~~~~~~~~~~~~

.. sample:: Networking/v2/subnets/create_with_host_routes.php

Read
----

.. sample:: Networking/v2/subnets/read.php

Update
------

.. sample:: Networking/v2/subnets/update.php

Delete
------

.. sample:: Networking/v2/subnets/delete.php
