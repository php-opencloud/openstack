Images
======

List images
-----------

- :apiref:`OpenStack/Compute/v2/Service.html#method_listImages`
- :sample:`compute/v2/list_images.php`

To list a collection of images, you run:

.. code-block:: php

    $images = $service->listImages();

    foreach ($images as $image) {

    }

Each iteration will return an :apiref:`Image instance <OpenStack/Compute/v2/Models/Image.html>`_.

.. include:: /common/generators.rst

Detailed information
~~~~~~~~~~~~~~~~~~~~

By default, only the ``id``, ``links`` and ``name`` attributes are returned. To return *all* information
for an image, you must enable detailed information, like so:

.. code-block:: php

    $images = $service->listImages(true);

Retrieve an image
-----------------

- :apiref:`OpenStack/Compute/v2/Service.html#method_getImage`
- :sample:`compute/v2/get_image.php`

When retrieving an image, sometimes you only want to operate on it - say to update or delete it. If this is the case,
then there is no need to perform an initial ``GET`` request to the server:

.. code-block:: php

    // Get an unpopulated object
    $image = $service->getImage(['id' => '{imageId}']);

If, however, you *do* want to retrieve all the details of a remote image from the API, you just call:

.. code-block:: php

    $image->retrieve();

which will update the state of the local object. This gives you an element of control over your app's performance.

Delete an image
---------------

- :apiref:`OpenStack/Compute/v2/Models/Image.html#method_delete`
- :sample:`compute/v2/delete_image.php`

.. code-block:: php

    $image->delete();

Retrieve metadata
-----------------

- :apiref:`OpenStack/Compute/v2/Models/Image.html#method_getMetadata`

This operation will retrieve the existing metadata for an image:

.. code-block:: php

    $metadata = $image->getMetadata();

Reset metadata
--------------

- :apiref:`OpenStack/Compute/v2/Models/Image.html#method_resetMetadata`
- :sample:`compute/v2/reset_image_metadata.php`

This operation will _replace_ all existing metadata with whatever is provided in the request. Any existing metadata
not specified in the request will be deleted.

.. code-block:: php

    $image->resetMetadata([
        'foo' => 'bar',
    ]);

Merge metadata
--------------

- :apiref:`OpenStack/Compute/v2/Models/Image.html#method_mergeMetadata`

This operation will _merge_ specified metadata with what already exists. Existing values will be overriden, new values
will be added. Any existing keys that are not specified in the request will remain unaffected.

.. code-block:: php

    $image->mergeMetadata([
        'foo' => 'bar',
    ]);

Retrieve image metadata item
----------------------------

- :apiref:`OpenStack/Compute/v2/Models/Image.html#method_getMetadataItem`

This operation allows you to retrieve the value for a specific metadata item:

.. code-block:: php

    $itemValue = $image->getMetadataItem('key');

Delete image metadata item
--------------------------

- :apiref:`OpenStack/Compute/v2/Models/Image.html#method_deleteMetadataItem`
- :sample:`compute/v2/delete_image_metadata_item.php`

This operation allows you to remove a specific metadata item:

.. code-block:: php

    $image->deleteMetadataItem('key');