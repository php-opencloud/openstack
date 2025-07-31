Projects
========

Projects represent the base unit of ownership in OpenStack, in that all resources in OpenStack should be owned
by a specific project. A project itself must be owned by a specific domain, and hence all project names
are not globally unique, but unique to their domain. If the domain for a project is not specified, then it is added
to the default domain.

.. osdoc:: https://docs.openstack.org/api-ref/identity/v3/index.html#projects

.. |models| replace:: projects

.. include:: /common/service.rst

Create
------

.. sample:: Identity/v3/projects/create.php

Read
----

.. sample:: Identity/v3/projects/read.php

Update
------

.. sample:: Identity/v3/projects/update.php

Delete
------

.. sample:: Identity/v3/projects/delete.php

List
----

.. sample:: Identity/v3/projects/list.php

List roles for project user
---------------------------

.. sample:: Identity/v3/projects/list_user_roles.php
.. refdoc:: OpenStack/Identity/v3/Models/Project.html#method_listUserRoles

Grant role to project user
--------------------------

.. sample:: Identity/v3/projects/grant_user_role.php
.. refdoc:: OpenStack/Identity/v3/Models/Project.html#method_grantUserRole

Check role for project user
---------------------------

.. sample:: Identity/v3/projects/check_user_role.php
.. refdoc:: OpenStack/Identity/v3/Models/Project.html#method_checkUserRole

Revoke role for project user
----------------------------

.. sample:: Identity/v3/projects/revoke_user_role.php
.. refdoc:: OpenStack/Identity/v3/Models/Project.html#method_revokeUserRole

List roles for project group
----------------------------

.. sample:: Identity/v3/projects/list_group_roles.php
.. refdoc:: OpenStack/Identity/v3/Models/Project.html#method_listGroupRoles

Grant role to project group
---------------------------

.. sample:: Identity/v3/projects/grant_group_role.php
.. refdoc:: OpenStack/Identity/v3/Models/Project.html#method_grantGroupRole

Check role for project group
----------------------------

.. sample:: Identity/v3/projects/check_group_role.php
.. refdoc:: OpenStack/Identity/v3/Models/Project.html#method_checkGroupRole

Revoke role for project group
-----------------------------

.. sample:: Identity/v3/projects/revoke_group_role.php
.. refdoc:: OpenStack/Identity/v3/Models/Project.html#method_revokeGroupRole