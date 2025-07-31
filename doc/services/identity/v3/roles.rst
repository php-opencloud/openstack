Roles
=====

OpenStack services typically determine whether a user’s API request should be allowed using Role Based Access Control (RBAC).
For OpenStack this means the service compares the roles that user has on the project (as indicated by the roles in the token),
against the roles required for the API in question (as defined in the service’s policy file). A user obtains roles
on a project by having these assigned to them via the Identity service API.

.. osdoc:: https://docs.openstack.org/api-ref/identity/v3/index.html#roles

.. |models| replace:: roles

.. include:: /common/service.rst

Create
------

.. sample:: Identity/v3/roles/create.php

List
----

.. sample:: Identity/v3/roles/list.php

List role assignments
---------------------

.. sample:: Identity/v3/roles/list_assignments.php
