Setup
=====

In order to access services you have to create the client object. To do it, you will need to provide the
following information:

* The identity service URL (``authUrl``)
* The region in which you want to operate (``region``)
* The credentials of the user you want to authenticate: ``user``, ``tokenId``, ``cachedToken``
  or ``application_credential``

Only the ``authUrl`` is mandatory to create the client. But you will have to provide the ``region`` and user
credentials to each service you create. So it is recommended to provide them when creating the client which
would propagate these options to each service.

There are different ways to provide the authentication credentials. See the :doc:`services/identity/v3/tokens`
section for the full list of options. You should provide credentials to the ``OpenStack`` constructor as an array
the same way you provide options to ``generateToken`` method of the ``Identity`` service.

Authenticate with user ID
~~~~~~~~~~~~~~~~~~~~~~~~~

.. sample:: Setup/user_id.php

Authenticate with username
~~~~~~~~~~~~~~~~~~~~~~~~~~

.. sample:: Setup/username.php

Authenticate application credential ID
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. sample:: Setup/application_credential_id.php

Generate token from ID
~~~~~~~~~~~~~~~~~~~~~~

.. sample:: Setup/from_id.php

Generate token scoped to project ID
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. sample:: Identity/v3/tokens/generate_token_scoped_to_project_id.php

Generate token scoped to project name
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. sample:: Identity/v3/tokens/generate_token_scoped_to_project_name.php


Here is an example of how to create a client with a user id and password:

.. code-block:: php

    $openstack = new OpenStack\OpenStack([
        'authUrl' => '{authUrl}',
        'region'  => '{region}',
        'user'    => [
            'id'       => '{userId}',
            'password' => '{password}'
        ],
    ]);

Here is an example of how to create a client with application credentials:

.. code-block:: php

    $openstack = new OpenStack\OpenStack([
        'authUrl' => '{authUrl}',
        'region'  => '{region}',
        'application_credential' => [
            'id'     => '{applicationCredentialId}',
            'secret' => '{secret}'
        ]
    ]);

You can specify the scope of the token:

.. code-block:: php

    $openstack = new OpenStack\OpenStack([
        'authUrl' => '{authUrl}',
        'region'  => '{region}',
        'user'    => [
            'id'       => '{userId}',
            'password' => '{password}'
        ],
        'scope' => [
            'project' => [
                'id' => '{projectId}'
            ],
        ],
    ]);
