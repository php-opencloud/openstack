Block Storage v2
================

Block Storage v2 API is deprecated since Pike release and was removed in the Xena release.
It is recommended to use Block Storage v3 API instead. However most of endpoints are identical, so if you still need
to use Block Storage v2 API, you can use the change ``$openstack->blockStorageV3()`` to ``$openstack->blockStorageV2()`` in examples.
In most cases it will work without any other changes.

.. sample:: BlockStorage/v2/create.php
