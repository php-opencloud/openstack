Containers
==========

Container defines a namespace for objects. An object with the same name in two different containers represents
two different objects.

.. osdoc:: https://docs.openstack.org/api-ref/object-store/#containers

.. |models| replace:: containers

.. include:: /common/service.rst

Read
----

.. sample:: ObjectStore/v1/containers/read.php

You can read the content of a container:

.. code-block:: php

    printf("%s container has %d objects and %d bytes",
        $container->name, $container->objectCount, $container->bytesUsed);

Delete
------

.. sample:: ObjectStore/v1/containers/delete.php

The API will only accept DELETE requests on containers when they are empty. If you have a container with any objects
inside, the operation will fail.

List
----

.. sample:: ObjectStore/v1/containers/list.php

When listing containers, you must be aware that not *all* information about a container is returned in a collection.
Very often only the object count, bytes used and container name will be exposed. If you would like to
access all of the remote state of a collection item, you can call ``retrieve`` like so:

.. code-block:: php

    foreach ($containers as $container) {
        $container->retrieve();
    }

If you have a large collection of containers, this will slow things down because you're issuing a HEAD request per
container.

.. include:: /common/generators.rst

Get metadata
------------

.. sample:: ObjectStore/v1/containers/get_metadata.php

The returned value will be a standard associative array, or hash, containing arbitrary key/value pairs. These will
correspond to the values set either when the container was created, or when a previous ``mergeMetadata`` or
``resetMetadata`` operation was called.

Replace all metadata with new values
------------------------------------

.. sample:: ObjectStore/v1/containers/reset_metadata.php

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

.. sample:: ObjectStore/v1/containers/merge_metadata.php

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
