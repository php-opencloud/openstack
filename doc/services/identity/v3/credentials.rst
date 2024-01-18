Credentials
===========

In exchange for a set of authentication credentials that the user submits, the Identity service generates and returns
a token. A token represents the authenticated identity of a user and, optionally, grants authorization on a specific
project or domain.

More information can be found in the `official documentation <https://docs.openstack.org/api-ref/identity/v3/index.html#credentials>`_.

List
----

.. sample:: Identity/v3/credentials/list.php

Create
------

Create a secret/access pair for use with ec2 style auth. This operation will generates a new set of credentials that
map the user/tenant pair.

.. sample:: Identity/v3/credentials/create.php

Read
----

Retrieve a user's access/secret pair by the access key.

.. sample:: Identity/v3/credentials/get.php

Update
------

.. sample:: Identity/v3/credentials/update.php

Delete
------

Delete a user's access/secret pair.

.. sample:: Identity/v3/credentials/delete.php
