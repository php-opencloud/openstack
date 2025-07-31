Servers
=======

Server is the main resource in the Compute API. It represents a virtual machine.

.. osdoc:: https://docs.openstack.org/api-ref/compute/#servers-servers

.. |models| replace:: servers

.. include:: /common/service.rst

List
----

To list a collection of servers, you run:

.. sample:: Compute/v2/servers/list.php

Each iteration will return a :apiref:Server instance <OpenStack/Compute/v2/Models/Server.html>.

.. include:: /common/generators.rst

Detailed information
~~~~~~~~~~~~~~~~~~~~

By default, only the id, links and name attributes are returned by the server. To return *all* information
for a server, you must enable detailed information, like so:

.. code-block:: php

    $servers = $service->listServers(true);

Filtering collections
~~~~~~~~~~~~~~~~~~~~~

By default, every server will be returned by the remote API. To filter the returned collection, you can provide query
parameters which are documented in the reference documentation.

.. code-block:: php

    use OpenStack\Common\DateTime;

    $servers = $service->listServers(false, [
        'changesSince' => DateTime::factory('yesterday')->toIso8601(),
        'flavorId'     => 'performance1-1',
    ]);

Create
------

The only attributes that are required when creating a server are a name, flavor ID and image ID. The simplest example
would therefore be this:

.. sample:: Compute/v2/servers/create.php

You can further configure your new server, however, by following the below sections, which instruct you how to add
specific functionality. They are interoperable and can work together.

Security groups
~~~~~~~~~~~~~~~

You can associate your new server with pre-existing security groups by specifying their _name_. One server can be
associated with multiple security groups:

.. code-block:: php

    $options['securityGroups'] = ['secGroup1', 'default', 'secGroup2'];

Networks
~~~~~~~~

By default, the server instance is provisioned with all isolated networks for the tenant. You can, however, configure
access by specifying which networks your VM is connected to. To do this, you can either:

- specify the UUID of a Neutron network. This is required if you omit the port ID.
- specify the UUID of a Neutron port. This is required if you omit the network ID. The port must exist and be in a DOWN state.

.. code-block:: php

    // Specifying the network
    $options['networks'] = [
        ['uuid' => '{network1Id}'],
        ['uuid' => '{network2Id}'],
    ];

    // Or, specifying the port:
    $options['networks'] = [
        ['port' => '{port1Id}'],
        ['port' => '{port2Id}'],
    ];

External devices and boot from volume
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

This option allows for the booting of the server from a volume. If specified, the volume status must be available, and
the volume attach_status in the OpenStack Block Storage DB must be detached.

For example, to boot a server from a Cinder volume:

.. code-block:: php

    $options['blockDeviceMapping'] = [
        [
            'deviceName'      => '/dev/sda1',
            'sourceType'      => 'volume',
            'destinationType' => 'volume',
            'uuid'            => '{volumeId}',
            'bootIndex'       => 0,
        ]
    ];

Personality files
~~~~~~~~~~~~~~~~~

Servers, as they're created, can be injected with arbitrary file data. To do this, you must specify the path and file
contents (text only) to inject into the server at launch. The maximum size of the file path data is 255 bytes. The
maximum limit refers to the number of bytes in the decoded data and not the number of characters in the encoded data.

The contents *must* be base-64 encoded.

.. code-block:: php

    $options['personality'] = [
        'path'     => '/etc/banner.txt',
        'contents' => base64_encode('echo "Hi!";'),
    ];

Metadata
~~~~~~~~

The API also supports the ability to label servers with arbitrary key/value pairs, known as metadata. To specify this
when the server is launched, use this option:

.. code-block:: php

    $options['metadata'] = [
        'foo' => 'bar',
        'baz' => 'bar',
    ];

Read
----

.. sample:: Compute/v2/servers/read.php

Update
------

The first step when updating a server is modifying the attributes you want updated. By default, only a server's name,
IPv4 and IPv6 IPs, and its auto disk config attributes can be edited.

.. sample:: Compute/v2/servers/update.php

Delete
---------------

To permanently delete a server:

.. sample:: Compute/v2/servers/delete.php

Retrieve metadata
-----------------

This operation will retrieve the existing metadata for a server:

.. sample:: Compute/v2/servers/get_server_metadata.php

Reset metadata
--------------

This operation will _replace_ all existing metadata with whatever is provided in the request. Any existing metadata
not specified in the request will be deleted.

.. sample:: Compute/v2/servers/reset_server_metadata.php

Merge metadata
--------------

This operation will _merge_ specified metadata with what already exists. Existing values will be overriden, new values
will be added. Any existing keys that are not specified in the request will remain unaffected.

.. sample:: Compute/v2/servers/merge_server_metadata.php

Retrieve metadata item
----------------------

This operation allows you to retrieve the value for a specific metadata item:

.. sample:: Compute/v2/servers/get_server_metadata_item.php

Delete metadata item
--------------------

This operation allows you to remove a specific metadata item:

.. sample:: Compute/v2/servers/delete_server_metadata_item.php

Change root password
--------------------

This operation will replace the root password for a server.

.. sample:: Compute/v2/servers/change_server_password.php

Reset server state
------------------

This operation will reset the state of the server.

.. sample:: Compute/v2/servers/reset_server_state.php

Reboot server
-------------

This operation will reboot a server. You can specify HARD reboot by passing argument Enum::REBOOT_HARD to the function.
Otherwise, the default is a SOFT reboot.

.. sample:: Compute/v2/servers/reboot_server.php

Rebuild server
--------------

Rebuilding a server will re-initialize the booting procedure for the server and effectively reinstall the operating
system. It will shutdown, re-image and then reboot your instance. Any data saved on your instance will be lost when
the rebuild is performed.

.. sample:: Compute/v2/servers/rebuild_server.php

Resize server
-------------

You can resize the flavor of a server by performing this operation. As soon the operation completes, the server will
transition to a VERIFY_RESIZE state and a VM status of RESIZED. You will either need to confirm or revert the
resize in order to continue.

.. sample:: Compute/v2/servers/resize_server.php

Confirm server resize
---------------------

Once a server has been resized, you can confirm the operation by calling this. The server must have the status of
VERIFY_RESIZE and a VM status of RESIZED. Once this operation completes, the server should transition to an
ACTIVE state and a migration status of confirmed.

.. sample:: Compute/v2/servers/confirm_server_resize.php

Revert server resize
--------------------

Once a server has been resized, you can revert the operation by calling this. The server must have the status of
VERIFY_RESIZE and a VM status of RESIZED. Once this operation completes, the server should transition to an
ACTIVE state and a migration status of reverted.

.. sample:: Compute/v2/servers/revert_server_resize.php

Create server image
-------------------

This operation will create a new server image. The only required option is the new image's name. You may also specify
additional metadata:

.. sample:: Compute/v2/images/create_server_image.php

List server IP addresses
------------------------

To list all the addresses for a specified server or a specified server and network:

.. code-block:: php

    $ipAddresses = $server->listAddresses();

    $public  = $ipAddresses['public'];
    $private = $ipAddresses['private'];

You can also refine by network label:

.. code-block:: php

    $ipAddresses = $server->listAddresses([
        'networkLabel' => '{networkLabel}',
    ]);

Suspend
-------

.. sample:: Compute/v2/servers/suspend.php

Resume
------

.. sample:: Compute/v2/servers/resume.php

