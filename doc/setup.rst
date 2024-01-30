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

Authenticate with username
--------------------------

The most common way to authenticate is using the username and password of the user. You should also provide the Domain ID
as usernames will not be unique across an entire OpenStack installation

.. sample:: Setup/username.php

Authenticate with user ID
-------------------------

.. sample:: Setup/user_id.php

Authenticate application credential ID
--------------------------------------

.. sample:: Setup/application_credential_id.php

Authenticate using token ID
---------------------------

If you already have a valid token, you can use it to authenticate.

.. sample:: Setup/token_id.php
