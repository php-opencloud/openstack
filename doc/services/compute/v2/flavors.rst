Flavors
=======

Flavors define the compute, memory, and storage capacity of nova computing instances. To put it simply, a flavor is
an available hardware configuration for a server. It defines the size of a virtual server that can be launched.

.. osdoc:: https://docs.openstack.org/nova/latest/user/flavors.html

.. |models| replace:: flavors

.. include:: /common/service.rst

Read
----

.. sample:: Compute/v2/flavors/read.php

List
----

.. sample:: Compute/v2/flavors/list.php

Each iteration will return a :php:class:`Flavor` instance <OpenStack/Compute/v2/Models/Flavor.html>.

.. include:: /common/generators.rst

Detailed information
~~~~~~~~~~~~~~~~~~~~

By default, only the ``id``, ``links`` and ``name`` attributes are returned. To return *all* information
for a flavor, you must pass ``true`` as the last parameter, like so:

.. code-block:: php

    $flavors = $service->listFlavors([], null, true);

