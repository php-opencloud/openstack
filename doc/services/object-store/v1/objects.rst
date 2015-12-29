Objects
=======

Show details for an object
--------------------------

.. sample:: object_store/v1/objects/get.php

At this point, the object returned is *empty* because we did not execute a HTTP request to receive the state of the
container from the API. This is in accordance with one of the SDK's general policies of not assuming too much at the
expense of performance.

To synchronize the local object's state with the remote API, you can run:

.. code-block:: php

    $object->retrieve();

    printf("%s/%s is %d bytes long and was last modified on %s",
        $object->containerName, $object->name, $object->contentLength, $object->lastModified);

and all of the local properties will match those of the remote resource. The ``retrieve`` call, although fetching all
of the object's metadata, will not download the object's content. To do this, see the next section.

Download an object
------------------

.. sample:: object_store/v1/objects/download.php

As you will notice, a Stream_ object is returned by this call. For more information about dealing with streams, please
consult `Guzzle's docs`_.

.. _Stream: https://github.com/guzzle/streams/blob/master/src/Stream.php
.. _Guzzle's docs: https://guzzle.readthedocs.org/en/5.3/streams.html

List objects
------------

.. sample:: object_store/v1/objects/list.php

When listing objects, you must be aware that not *all* information about a container is returned in a collection.
Very often only the MD5 hash, last modified date, bytes used, content type and object name will be
returned. If you would like to access all of the remote state of a collection item, you can call ``retrieve`` like so:

.. code-block:: php

    foreach ($objects as $object) {
        // To retrieve metadata
        $object->retrieve();
    }

If you have a large collection of $object, this will slow things down because you're issuing a HEAD request per object.

Create an object
------------

.. sample:: object_store/v1/objects/create.php

Copy object
-----------

.. sample:: object_store/v1/objects/copy.php

Delete object
-------------

.. sample:: object_store/v1/objects/delete.php

Get metadata
------------

.. sample:: object_store/v1/objects/get_metadata.php

The returned value will be a standard associative array, or hash, containing arbitrary key/value pairs. These will
correspond to the values set either when the object was created, or when a previous ``mergeMetadata`` or
``resetMetadata`` operation was called.

Replace all metadata with new values
------------------------------------

.. sample:: object_store/v1/objects/reset_metadata.php

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

.. sample:: object_store/v1/objects/merge_metadata.php

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
