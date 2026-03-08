Server Groups
=============

Server groups let you express affinity or anti-affinity placement rules for Nova servers.

.. osdoc:: https://docs.openstack.org/api-ref/compute/#server-groups-os-server-groups

.. |models| replace:: server groups

.. include:: /common/service.rst

List
----

.. sample:: Compute/v2/server_groups/list.php

Each iteration will return a :php:class:`ServerGroup` instance <OpenStack/Compute/v2/Models/ServerGroup.html>.

.. include:: /common/generators.rst

Admin-only listing across all projects is also available:

.. code-block:: php

    $serverGroups = $service->listServerGroups([
        'allProjects' => true,
    ]);

Create
------

Use ``name`` and ``policies`` for the baseline Compute API:

.. sample:: Compute/v2/server_groups/create.php

Microversion 2.64+
~~~~~~~~~~~~~~~~~~

If the Compute service is created with microversion ``2.64`` or later, you can use the singular ``policy`` field and
optional ``rules`` object instead:

.. sample:: Compute/v2/server_groups/create_2_64.php

When Nova responds with the newer singular ``policy`` field, the SDK also exposes that value as the first item in
``policies`` for compatibility with the older response shape.

Create A Server In A Group
--------------------------

To place a server into an existing server group, pass the server group UUID through ``schedulerHints.group`` when you
create the server:

.. sample:: Compute/v2/server_groups/create_server.php

Read
----

.. sample:: Compute/v2/server_groups/read.php

Delete
------

.. sample:: Compute/v2/server_groups/delete.php
