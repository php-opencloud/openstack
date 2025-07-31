Groups
======

Groups are a container representing a collection of users. A group itself must be owned by a specific domain,
and hence all group names are not globally unique, but only unique to their domain.

.. osdoc:: https://docs.openstack.org/api-ref/identity/v3/index.html#groups

.. |models| replace:: groups

.. include:: /common/service.rst

Create
------

.. sample:: Identity/v3/groups/create.php

Read
----

.. sample:: Identity/v3/groups/read.php

Update
------

.. sample:: Identity/v3/groups/update.php

Delete
------

.. sample:: Identity/v3/groups/delete.php

List
----

.. sample:: Identity/v3/groups/list.php

List users in a group
---------------------

.. sample:: Identity/v3/groups/list_users.php
.. refdoc:: OpenStack/Identity/v3/Models/Group.html#method_listUsers

Add user to group
-----------------

.. sample:: Identity/v3/groups/add_user.php
.. refdoc:: OpenStack/Identity/v3/Models/Group.html#method_addUser

Remove user from group
----------------------

.. sample:: Identity/v3/groups/remove_user.php
.. refdoc:: OpenStack/Identity/v3/Models/Group.html#method_removeUser

Check user membership in a group
--------------------------------

.. sample:: Identity/v3/groups/check_user_membership.php
.. refdoc:: OpenStack/Identity/v3/Models/Group.html#method_checkMembership
