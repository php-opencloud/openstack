Create Service
==============

In order to work with the service you have to :doc:`setup the client </setup>` first.

Service can be created via :substitution-code:`|method|()` method of the ``OpenStack`` object.

.. code-block:: php
    :substitutions:

    $service = $openstack->|method|();

A list of additional options can be passed to the method. For example, to change the region:

.. code-block:: php
    :substitutions:

    $service = $openstack->|method|(['region' => '{region}']);
