Create Service
==============

OpenStack Identity API v3. It is used to manage the `OpenStack Keystone service <https://docs.openstack.org/keystone/latest/index.html>`_.

More information can be found in the `official documentation <https://docs.openstack.org/api-ref/identity/v3/index.html>`_.

Service can be created via ``identityV3()`` method of the ``OpenStack`` object:

.. sample:: Identity/v3/create.php

A list of additional options can be passed to the method. For example, to change the region:

.. sample:: Identity/v3/create_with_region.php
