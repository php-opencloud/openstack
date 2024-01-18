Application Credentials
=======================

Application credentials provide a way to delegate a user’s authorization to an application without sharing the user’s
password authentication. This is a useful security measure, especially for situations where the user’s identification
is provided by an external source, such as LDAP or a single-sign-on service. Instead of storing user passwords in
config files, a user creates an application credential for a specific project, with all or a subset of the role assignments
they have on that project, and then stores the application credential identifier and secret in the config file.

.. osdoc:: https://docs.openstack.org/keystone/latest/user/application_credentials.html

.. |models| replace:: application credentials

.. include:: /common/service.rst

Create
------

.. sample:: Identity/v3/application_credentials/create.php

Read
----

.. sample:: Identity/v3/application_credentials/read.php

Delete
------

.. sample:: Identity/v3/application_credentials/delete.php

