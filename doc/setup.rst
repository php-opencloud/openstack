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

Authenticate
------------

There are different ways to provide the authentication credentials. See the :doc:`services/identity/v3/tokens`
section for the full list of options. You should provide credentials to the ``OpenStack`` constructor as an array
the same way you provide options to ``generateToken`` method of the ``Identity`` service.

By username
~~~~~~~~~~~

The most common way to authenticate is using the username and password of the user. You should also provide the Domain ID
as usernames will not be unique across an entire OpenStack installation

.. sample:: Setup/username.php

By user ID
~~~~~~~~~~

.. sample:: Setup/user_id.php

By application credential ID
~~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. sample:: Setup/application_credential_id.php

By token ID
~~~~~~~~~~~

If you already have a valid token, you can use it to authenticate.

.. sample:: Setup/token_id.php

Other options
-------------

For production environments it is recommended to decrease error reporting not to expose sensitive information. It can be done
by setting the ``errorVerbosity`` key to ``0`` in the options array. It is set to 2 by default.

.. code-block:: php

    $openstack = new OpenStack\OpenStack([
        'errorVerbosity' => 0,
        // other options
    ]);