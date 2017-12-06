Tokens
======

Authenticate (generate) token
-----------------------------

.. refdoc:: OpenStack/Identity/v3/Service.html#method_createService


Generate token with user ID
~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. sample:: identity/v3/tokens/generate_token_with_user_id.php

Generate token with username
~~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. sample:: identity/v3/tokens/generate_token_with_username.php

Generate token from ID
~~~~~~~~~~~~~~~~~~~~~~

.. sample:: identity/v3/tokens/generate_token_from_id.php

Generate token scoped to project ID
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. sample:: identity/v3/tokens/generate_token_scoped_to_project_id.php

Generate token scoped to project name
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. sample:: identity/v3/tokens/generate_token_scoped_to_project_name.php

Validate token
--------------

.. sample:: identity/v3/tokens/validate_token.php
.. refdoc:: OpenStack/Identity/v3/Service.html#method_validateToken

Revoke token
------------

.. sample:: identity/v3/tokens/revoke_token.php
.. refdoc:: OpenStack/Identity/v3/Service.html#method_revokeToken

Cache authentication token
--------------------------

Use case
~~~~~~~~

Before one can start calling any API, a very first step that this SDK will do is to authenticate with Identity service
using user's credential.

If the user's credential is valid, Identity service responses with an authentication token embedded in X-Auth-Token
header and services catalog. The SDK will then use this authentication token and services catalog in all subsequent API
calls.

This setup typically works well for command line type of applications. However, for web-based applications, performance
is undesirable since authentication step adds ~100ms to the response time.

In order to improve performance, SDK allows users to export and store authentication token and re-use it when it is
still valid.


Generate token and persist to file
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. sample:: identity/v3/tokens/export_authentication_token.php


For scalability, it is recommended that cached tokens are stored in persistent storage such as memcache or redis instead
of local file.

Initialize Open Stack using cached authentication token
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. sample:: identity/v3/tokens/use_cached_authentication_token.php
