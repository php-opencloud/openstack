Images
======

A collection of files for a specific operating system (OS) that you use to create or rebuild a server.
OpenStack provides pre-built images. You can also create custom images, or snapshots, from servers
that you have launched. Custom images can be used for data backups or as “gold” images for additional servers.

.. osdoc:: https://docs.openstack.org/api-ref/image/v2/index.html#images

.. |models| replace:: images

.. include:: /common/service.rst

Create
------

The only required attribute when creating a new image is ``name``.

.. sample:: Images/v2/images/create.php

Read
----

.. sample:: Images/v2/images/read.php

Update
------

.. sample:: Images/v2/images/update.php

Delete
------

.. sample:: Images/v2/images/delete.php

List
----

.. sample:: Images/v2/images/list.php

.. include:: /common/generators.rst

List images sorted
~~~~~~~~~~~~~~~~~~

Possible values for sort_key are:

* name

Possible values for sort_dir are:

* asc
* desc

.. sample:: Images/v2/images/list_sorted.php

Reactivate
----------

.. sample:: Images/v2/images/reactivate.php

Deactivate
----------

If you try to download a deactivated image, a Forbidden error is returned.

.. sample:: Images/v2/images/deactivate.php

Upload binary data
------------------

Before you can store binary image data, you must meet the following preconditions:

* The image must exist.
* You must set the disk and container formats in the image.
* The image status must be ``queued``.
* Your image storage quota must be sufficient.

The size of the data that you want to store must not exceed the size that the Image service allows.

.. sample:: Images/v2/images/upload_binary_data.php

Download binary data
--------------------

.. sample:: Images/v2/images/download_binary_data.php
