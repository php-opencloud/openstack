LoadBalancers
=============

.. warning::
    Load balancing functions accessed via the neutron endpoint are deprecated and will be removed in a future release.
    Users are strongly encouraged to migrate to using the octavia endpoint.
    This library does not support the octavia endpoint yet.
    Consider `helping <https://github.com/php-opencloud/openstack/pulls>`_ us to implement it .

.. osdoc:: https://docs.openstack.org/api-ref/network/v2/index.html#load-balancer-as-a-service-2-0-deprecated

.. |models| replace:: load balancers

.. include:: /common/service.rst

Create
------

.. sample:: Networking/v2/lbaas/loadbalancers/create.php

Read
----

.. sample:: Networking/v2/lbaas/loadbalancers/read.php

Update
------

.. sample:: Networking/v2/lbaas/loadbalancers/update.php

Delete
------

.. sample:: Networking/v2/lbaas/loadbalancers/delete.php

List
----

.. sample:: Networking/v2/lbaas/loadbalancers/list.php

Add Listener
------------

.. sample:: Networking/v2/lbaas/loadbalancers/add_listener.php

Get Stats
---------

.. sample:: Networking/v2/lbaas/loadbalancers/get_stats.php

Get Status Tree
--------------------------------

.. sample:: Networking/v2/lbaas/loadbalancers/get_statuses.php
