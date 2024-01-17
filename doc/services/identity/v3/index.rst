Identity v3
===========

Creating the service
--------------------

Service can be created via `identityV3` method of `OpenStack` object:

.. code-block:: php

    $service = $openstack->identityV3();

A list of additional options can be passed to the method. For example, to change the region:

.. code-block:: php

    $service = $openstack->identityV3(['region' => '{region}']);

.. toctree::
  :maxdepth: 3

  application-credentials
  credentials
  domains
  endpoints
  groups
  policies
  projects
  roles
  services
  tokens
  users
