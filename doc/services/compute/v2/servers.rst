Servers
=======

Server states
-------------

Servers contain a status attribute that indicates the current server state. You can filter on the server status when
you complete a list servers request. The server status is returned in the response body. The server status is one of
the following values:

+---------------+------------------------------------------------------------------------------------------------------+
| State         | Description                                                                                          |
+===============+======================================================================================================+
| ACTIVE        | The server is active.                                                                                |
+---------------+------------------------------------------------------------------------------------------------------+
| BUILDING      | The server has not finished the original build process.                                              |
+---------------+------------------------------------------------------------------------------------------------------+
| DELETED       | The server is permanently deleted.                                                                   |
+---------------+------------------------------------------------------------------------------------------------------+
| ERROR         | The server is in error.                                                                              |
+---------------+------------------------------------------------------------------------------------------------------+
| HARD_REBOOT   | The server is hard rebooting. This is equivalent to pulling the power plug on a physical server,     |
|               | plugging it back in, and rebooting it.                                                               |
+---------------+------------------------------------------------------------------------------------------------------+
| PASSWORD      | The password is being reset on the server.                                                           |
+---------------+------------------------------------------------------------------------------------------------------+
| PAUSED        | In a paused state, the state of the server is stored in RAM. A paused server continues to run in     |
|               | frozen state.                                                                                        |
+---------------+------------------------------------------------------------------------------------------------------+
| REBOOT        | The server is in a soft reboot state. A reboot command was passed to the operating system.           |
+---------------+------------------------------------------------------------------------------------------------------+
| REBUILD       | The server is currently being rebuilt from an image.                                                 |
+---------------+------------------------------------------------------------------------------------------------------+
| RESCUED       | The server is in rescue mode. A rescue image is running with the original server image attached.     |
+---------------+------------------------------------------------------------------------------------------------------+
| RESIZED       | Server is performing the differential copy of data that changed during its initial copy. Server is   |
|               | down for this stage.                                                                                 |
+---------------+------------------------------------------------------------------------------------------------------+
| REVERT_RESIZE | The resize or migration of a server failed for some reason. The destination server is being cleaned  |
|               | up and the original source server is restarting.                                                     |
+---------------+------------------------------------------------------------------------------------------------------+
| SOFT_DELETED  | The server is marked as deleted but the disk images are still available to restore.                  |
+---------------+------------------------------------------------------------------------------------------------------+
| STOPPED       | The server is powered off and the disk image still persists.                                         |
+---------------+------------------------------------------------------------------------------------------------------+
| SUSPENDED     | The server is suspended, either by request or necessity. This status appears for only the following  |
|               | hypervisors: XenServer/XCP, KVM, and ESXi. Administrative users may suspend an instance if it is     |
|               | infrequently used or to perform system maintenance. When you suspend an instance, its VM state is    |
|               | stored on disk, all memory is written to disk, and the virtual machine is stopped. Suspending an     |
|               | instance is similar to placing a device in hibernation; memory and vCPUs become available to create  |
|               | other instances.                                                                                     |
+---------------+------------------------------------------------------------------------------------------------------+
| UNKNOWN       | The state of the server is unknown. Contact your cloud provider.                                     |
+---------------+------------------------------------------------------------------------------------------------------+
| VERIFY_RESIZE | System is awaiting confirmation that the server is operational after a move or resize.               |
+---------------+------------------------------------------------------------------------------------------------------+

Listing servers
---------------

- :apiref:`OpenStack/Compute/v2/Service.html#method_listServers`
- :sample:`compute/v2/list_servers.php`

To list a collection of servers, you run:

.. code-block:: php

    $servers = $service->listServers();

    foreach ($servers as $server) {

    }

Each iteration will return a :apiref:`Server instance <OpenStack/Compute/v2/Models/Server.html>`.

.. include:: /common/generators.rst


Detailed information
~~~~~~~~~~~~~~~~~~~~

By default, only the ``id``, ``links`` and ``name`` attributes are returned by the server. To return *all* information
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

Create a server
---------------

- :apiref:`OpenStack/Compute/v2/Service.html#method_createServer`
- :sample:`compute/v2/create_server.php`

The only attributes that are required when creating a server are a name, flavor ID and image ID. The simplest example
would therefore be this:

.. code-block:: php

    $options = [
        'name'     => '{name}',
        'flavorId' => '{flavorId}',
        'imageId'  => '{imageId}'
    ];

    $server = $service->createServer($options);

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
- specify the UUID of a Neutron port. This is required if you omit the network ID. The port must exist and be in a ``DOWN`` state.

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
the volume ``attach_status`` in the OpenStack Block Storage DB must be detached.

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

Retrieve a server
-----------------

- :apiref:`OpenStack/Compute/v2/Service.html#method_getServer`
- :sample:`compute/v2/get_server.php`

When retrieving a server, sometimes you only want to operate on it - say to update or delete it. If this is the case,
then there is no need to perform an initial ``GET`` request to the server:

.. code-block:: php

    // Get an unpopulated object
    $server = $service->getServer(['id' => '{serverId}']);

If, however, you *do* want to retrieve all the details of a remote server from the API, you just call:

.. code-block:: php

    $server->retrieve();

which will update the state of the local object. This gives you an element of control over your app's performance.


Update a server
---------------

- :apiref:`OpenStack/Compute/v2/Models/Server.html#method_update`
- :sample:`compute/v2/update_server.php`

The first step when updating a server is modifying the attributes you want updated. By default, only a server's name,
IPv4 and IPv6 IPs, and its auto disk config attributes can be edited.

.. code-block:: php

    $server->name = 'new name!';
    $server->ipv4 = '10.0.0.51';

Once this is done, you can publish the updates to the API:

.. code-block:: php

    $server->update();

Delete a server
---------------

- :apiref:`OpenStack/Compute/v2/Models/Server.html#method_delete`
- :sample:`compute/v2/delete_server.php`

To permanently delete a server:

.. code-block:: php

    $server->delete();

Retrieve metadata
-----------------

- :apiref:`OpenStack/Compute/v2/Models/Server.html#method_getMetadata`
- :sample:`compute/v2/get_server_metadata.php`

This operation will retrieve the existing metadata for a server:

.. code-block:: php

    $metadata = $server->getMetadata();

Reset metadata
--------------

- :apiref:`OpenStack/Compute/v2/Models/Server.html#method_resetMetadata`
- :sample:`compute/v2/reset_server_metadata.php`

This operation will _replace_ all existing metadata with whatever is provided in the request. Any existing metadata
not specified in the request will be deleted.

.. code-block:: php

    $server->resetMetadata([
        'foo' => 'bar',
    ]);

Merge metadata
--------------

- :apiref:`OpenStack/Compute/v2/Models/Server.html#method_mergeMetadata`
- :sample:`compute/v2/merge_server_metadata.php`

This operation will _merge_ specified metadata with what already exists. Existing values will be overriden, new values
will be added. Any existing keys that are not specified in the request will remain unaffected.

.. code-block:: php

    $server->mergeMetadata([
        'foo' => 'bar',
    ]);

Retrieve metadata item
----------------------

- :apiref:`OpenStack/Compute/v2/Models/Server.html#method_getMetadataItem`
- :sample:`compute/v2/get_server_metadata_item.php`

This operation allows you to retrieve the value for a specific metadata item:

.. code-block:: php

    $itemValue = $server->getMetadataItem('key');

Delete metadata item
--------------------

- :apiref:`OpenStack/Compute/v2/Models/Server.html#method_deleteMetadataItem`
- :sample:`compute/v2/delete_server_metadata_item.php`

This operation allows you to remove a specific metadata item:

.. code-block:: php

    $server->deleteMetadataItem('key');

Change root password
--------------------

- :apiref:`OpenStack/Compute/v2/Models/Server.html#method_changePassword`
- :sample:`compute/v2/change_server_password.php`

This operation will replace the root password for a server.

.. code-block:: php

    $server->changePassword('{newPassword}');

Reboot server
-------------

- :apiref:`OpenStack/Compute/v2/Models/Server.html#method_reboot`
- :sample:`compute/v2/reboot_server.php`

This operation will reboot a server. Please be aware that you must specify whether you want to initiate a ``HARD`` or
``SOFT`` reboot (you specify this as a string argument).

.. code-block:: php

    use OpenStack\Compute\v2\Enum;

    $server->reboot(Enum::REBOOT_SOFT);

Rebuild server
--------------

- :apiref:`OpenStack/Compute/v2/Models/Server.html#method_rebuild`
- :sample:`compute/v2/rebuild_server.php`

Rebuilding a server will re-initialize the booting procedure for the server and effectively reinstall the operating
system. It will shutdown, re-image and then reboot your instance. Any data saved on your instance will be lost when
the rebuild is performed.

.. code-block:: php

    $server->rebuild([
        'imageId'   => '{imageId}',
        'name'      => '{newName}',
        'adminPass' => '{adminPass}',
    ]);

Resize server
-------------

- :apiref:`OpenStack/Compute/v2/Models/Server.html#method_resize`
- :sample:`compute/v2/resize_server.php`

You can resize the flavor of a server by performing this operation. As soon the operation completes, the server will
transition to a ``VERIFY_RESIZE`` state and a VM status of ``RESIZED``. You will either need to confirm or revert the
resize in order to continue.

.. code-block:: php

    $server->resize('{newFlavorId}');

Confirm server resize
---------------------

- :apiref:`OpenStack/Compute/v2/Models/Server.html#method_confirmResize`
- :sample:`compute/v2/confirm_server_resize.php`

Once a server has been resized, you can confirm the operation by calling this. The server must have the status of
``VERIFY_RESIZE`` and a VM status of ``RESIZED``. Once this operation completes, the server should transition to an
``ACTIVE`` state and a migration status of ``confirmed``.

.. code-block:: php

    $server->confirmResize();

Revert server resize
--------------------

- :apiref:`OpenStack/Compute/v2/Models/Server.html#method_revertResize`
- :sample:`compute/v2/revert_server_resize.php`

Once a server has been resized, you can revert the operation by calling this. The server must have the status of
``VERIFY_RESIZE`` and a VM status of ``RESIZED``. Once this operation completes, the server should transition to an
``ACTIVE`` state and a migration status of ``reverted``.

.. code-block:: php

    $server->revertResize();

Create server image
-------------------

- :apiref:`OpenStack/Compute/v2/Models/Server.html#method_createImage`
- :sample:`compute/v2/create_server_image.php`

This operation will create a new server image. The only required option is the new image's name. You may also specify
additional metadata:

.. code-block:: php

    $server->createImage([
        'name' => 'my-image',
        'metadata' => [
            'foo' => 'bar',
        ]
    ]);

List server IP addresses
------------------------

- :apiref:`OpenStack/Compute/v2/Models/Server.html#method_listAddresses`
- :sample:`compute/v2/list_server_addresses.php`

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

Further links
-------------

* Reference docs for Server class
* API docs