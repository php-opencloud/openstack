Objects
=======

Object stores data content, such as documents, images, and so on. You can also store custom metadata with an object.

.. osdoc:: https://docs.openstack.org/api-ref/object-store/#objects

.. |models| replace:: objects

.. include:: /common/service.rst

Create
------

When creating an object, you can upload its content according to a string representation:

.. sample:: ObjectStore/v1/objects/create.php

If that is not optimal or convenient, you can use a stream instead. Any instance of ``\Psr\Http\Message\StreamInterface``
is acceptable. For example, to use a normal Guzzle stream:

.. sample:: ObjectStore/v1/objects/create_from_stream.php

Create a large object (over 5GB)
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

For large objects (those over 5GB), you will need to use a concept in Swift called Dynamic Large Objects (DLO). When
uploading, this is what happens under the hood:

1. The large file is separated into smaller segments
2. Each segment is uploaded
3. A manifest file is created which, when requested by clients, will concatenate all the segments as a single file

To upload a DLO, you need to call:

.. sample:: ObjectStore/v1/objects/create_large_object.php

Read
----

.. sample:: ObjectStore/v1/objects/read.php

You can read properties of an object:

.. code-block:: php

    printf("%s/%s is %d bytes long and was last modified on %s",
        $object->containerName, $object->name, $object->contentLength, $object->lastModified);

Delete
------

.. sample:: ObjectStore/v1/objects/delete.php

List
----

.. sample:: ObjectStore/v1/objects/list.php

When listing objects, you must be aware that not *all* information about a container is returned in a collection.
Very often only the MD5 hash, last modified date, bytes used, content type and object name will be
returned. If you would like to access all of the remote state of a collection item, you can call ``retrieve`` like so:

.. code-block:: php

    foreach ($objects as $object) {
        // To retrieve metadata
        $object->retrieve();
    }

If you have a large collection of $object, this will slow things down because you're issuing a HEAD request per object.

.. include:: /common/generators.rst

Download an object
------------------

.. sample:: ObjectStore/v1/objects/download.php

As you will notice, a Stream_ object is returned by this call. For more information about dealing with streams, please
consult `Guzzle's docs`_.

By default, the whole body of the object is fetched before the function returns, set the ``'requestOptions'`` key of
parameter ``$data`` to ``['stream' => true]`` to get the stream before the end of download.

.. _Stream: https://github.com/guzzle/streams/blob/master/src/Stream.php
.. _Guzzle's docs: https://guzzle.readthedocs.org/en/5.3/streams.html

Copy object
-----------

.. sample:: ObjectStore/v1/objects/copy.php

Get metadata
------------

.. sample:: ObjectStore/v1/objects/get_metadata.php

The returned value will be a standard associative array, or hash, containing arbitrary key/value pairs. These will
correspond to the values set either when the object was created, or when a previous ``mergeMetadata`` or
``resetMetadata`` operation was called.

Replace all metadata with new values
------------------------------------

.. sample:: ObjectStore/v1/objects/reset_metadata.php
.. refdoc:: OpenStack/ObjectStore/v1/Models/StorageObject.html#method_resetMetadata

In order to replace all existing metadata with a set of new values, you can use this operation. Any existing metadata
items which not specified in the new set will be removed. For example, say an account has the following metadata
already set:

::

    Foo: value1
    Bar: value2

and you *reset* the metadata with these values:

::

    Foo: value4
    Baz: value3

the metadata of the account will now be:

::

    Foo: value4
    Baz: value3


Merge new metadata values with existing
---------------------------------------

.. sample:: ObjectStore/v1/objects/merge_metadata.php
.. refdoc:: OpenStack/ObjectStore/v1/Models/StorageObject.html#method_mergeMetadata

In order to merge a set of new metadata values with the existing metadata set, you can use this operation. Any existing
metadata items which are not specified in the new set will be preserved. For example, say an account has the following
metadata already set:

::

    Foo: value1
    Bar: value2

and you merge them with these values:

::

    Foo: value4
    Baz: value3

the metadata of the account will now be:

::

    Foo: value4
    Bar: value2
    Baz: value3
