Application Credentials
=======================

Application credentials provide a way to delegate a user’s authorization to an application without sharing the user’s
password authentication. This is a useful security measure, especially for situations where the user’s identification
is provided by an external source, such as LDAP or a single-sign-on service. Instead of storing user passwords in
config files, a user creates an application credential for a specific project, with all or a subset of the role assignments
they have on that project, and then stores the application credential identifier and secret in the config file.

More information can be found in the `official documentation <https://docs.openstack.org/keystone/latest/user/application_credentials.html>`_.

You must :doc:`create the service <services/identity/v3/create>`_ first to use this resource.

Create
------

.. sample:: Identity/v3/application_credentials/add_application_credential.php

Read
----

.. sample:: Identity/v3/application_credentials/show_application_credential.php

Delete
------

.. sample:: Identity/v3/application_credentials/delete_application_credential.php
