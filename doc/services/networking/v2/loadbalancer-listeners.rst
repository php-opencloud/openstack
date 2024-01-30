LoadBalancer Listeners
======================

.. warning::
    Load balancing functions accessed via the neutron endpoint are deprecated and will be removed in a future release.
    Users are strongly encouraged to migrate to using the octavia endpoint.
    This library does not support the octavia endpoint yet.
    Consider `helping <https://github.com/php-opencloud/openstack/pulls>`_ us to implement it .

.. osdoc:: https://docs.openstack.org/api-ref/network/v2/index.html#load-balancer-as-a-service-2-0-deprecated

Create
------

.. sample:: Networking/v2/lbaas/listeners/create.php

Read
----

.. sample:: Networking/v2/lbaas/listeners/read.php

Update
------

.. sample:: Networking/v2/lbaas/listeners/update.php

Delete
------

.. sample:: Networking/v2/lbaas/listeners/delete.php

List
----

.. sample:: Networking/v2/lbaas/listeners/list.php
