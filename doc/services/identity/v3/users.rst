Users
=====

A user is an individual API consumer that is owned by a domain. A role explicitly associates a user with projects
or domains. A user with no assigned roles has no access to OpenStack resources.

.. osdoc:: https://docs.openstack.org/api-ref/identity/v3/index.html#users

.. |models| replace:: users

.. include:: /common/service.rst

Create
------

.. sample:: Identity/v3/users/create.php

Read
----

.. sample:: Identity/v3/users/read.php

Update
------

.. sample:: Identity/v3/users/update.php

Delete
------

.. sample:: Identity/v3/users/delete.php

List
----

.. sample:: Identity/v3/users/list.php

List groups for user
--------------------

.. sample:: Identity/v3/users/list_groups.php

List projects for user
----------------------

.. sample:: Identity/v3/users/list_projects.php
