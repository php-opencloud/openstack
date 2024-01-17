Quick start
===========

Requirements
------------

* PHP >= 7, < 9
* cURL extension

Installation
------------

You must install this library through Composer:

.. code-block:: bash

    composer require php-opencloud/openstack

If you do not have Composer installed, please read the `Composer installation instructions`_.

Include the autoloader
----------------------

Once you have installed the SDK as a dependency of your project, you will need to load Composer’s autoloader
(which registers all the required namespaces). To do this, place the following line of PHP code at the top of your
application’s PHP files:

.. code-block:: php

    require 'vendor/autoload.php';

This assumes your application's PHP files are located in the same folder as ``vendor/``. If your files are located
elsewhere, please supply the path to vendor/autoload.php in the require statement above.

Creating a client
-----------------

To create a client, you will need to provide the following information:

* The identity service URL (``authUrl``)
* The region in which you want to operate (``region``)
* The credentials of the user you want to authenticate: ``user``, ``tokenId``, ``cachedToken``
  or ``application_credential``

Only the ``authUrl`` is mandatory to create a client. But you will have to provide the ``region`` and ``user``
to each service you create. So it is recommended to provide them when creating the client.

There are different ways to provide the authentication credentials. See the :doc:`services/identity/v3/tokens`
section for the full list of options. You should provide credentials to the `OpenStack` constructor as an array
the same way you provide options to ``generateToken`` method of the ``Identity`` service.

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

.. _Composer installation instructions: https://getcomposer.org/doc/00-intro.md
