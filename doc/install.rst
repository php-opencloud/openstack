Quick Start
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

.. _Composer installation instructions: https://getcomposer.org/doc/00-intro.md