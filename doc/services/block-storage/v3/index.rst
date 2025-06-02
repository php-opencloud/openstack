Block Storage v3
================

OpenStack Block Storage API v3 (Cinder). Cinder is the OpenStack Block Storage service for providing volumes
to Nova virtual machines, Ironic bare metal hosts, containers and more.

.. note::

    By default we are creating Cinder service with ``cinderv3`` name and ``volumev3`` type.
    This is left for backward compatibility and would be changed on the next major release of OpenStack SDK.
    It's made to be compatible with the old OpenStack installations where the 2nd version of Cinder was also installed.
    Check your service name and type. New installations would use ``cinder`` name and ``block-storage`` type.

.. osdoc:: https://docs.openstack.org/api-ref/block-storage/v3/

.. toctree::
    :maxdepth: 3

    create
    volumes
    volume-types
    snapshots
