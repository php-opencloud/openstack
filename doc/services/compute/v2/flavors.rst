Flavors
=======

List flavors
------------

- :apiref:`OpenStack/Compute/v2/Service.html#method_listFlavors`
- :sample:`compute/v2/list_flavors.php`

To list a collection of flavors, you run:

.. code-block:: php

    $flavors = $service->listFlavors();

    foreach ($flavors as $flavor) {

    }

Each iteration will return a :apiref:`Flavor instance <OpenStack/Compute/v2/Models/Flavor.html>`.

.. include:: /common/generators.rst

Detailed information
~~~~~~~~~~~~~~~~~~~~

By default, only the ``id``, ``links`` and ``name`` attributes are returned. To return *all* information
for a flavor, you must enable detailed information, like so:

.. code-block:: php

    $flavors = $service->listFlavors(true);

Retrieve a flavor
-----------------

- :apiref:`OpenStack/Compute/v2/Service.html#method_getFlavor`
- :sample:`compute/v2/get_flavor.php`

When retrieving a flavor, sometimes you only want to operate on it. If this is the case,
then there is no need to perform an initial ``GET`` request to the server:

.. code-block:: php

    // Get an unpopulated object
    $flavor = $service->getFlavor(['id' => '{flavorId}']);

If, however, you *do* want to retrieve all the details of a remote flavor from the API, you just call:

.. code-block:: php

    $flavor->retrieve();

which will update the state of the local object. This gives you an element of control over your app's performance.
