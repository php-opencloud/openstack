Endpoints
=========

Each service should have one or more related endpoints. An endpoint is essentially a base URL for
an API, along with some metadata about the endpoint itself and represents a set of URL endpoints for
OpenStack web services.

.. osdoc:: https://docs.openstack.org/api-ref/identity/v3/index.html#service-catalog-and-endpoints

.. |models| replace:: endpoints

.. include:: /common/service.rst


Add endpoints
-------------

.. sample:: Identity/v3/endpoints/add_endpoint.php
.. refdoc:: OpenStack/Identity/v3/Service.html#method_createEndpoint

Get endpoint
------------

.. sample:: Identity/v3/endpoints/get_endpoint.php
.. refdoc:: OpenStack/Identity/v3/Service.html#method_getEndpoint

List endpoints
--------------

.. sample:: Identity/v3/endpoints/list_endpoints.php
.. refdoc:: OpenStack/Identity/v3/Service.html#method_listEndpoints

Update endpoint
---------------

.. sample:: Identity/v3/endpoints/update_endpoint.php
.. refdoc:: OpenStack/Identity/v3/Models/Endpoint.html#method_update

Delete endpoint
---------------

.. sample:: Identity/v3/endpoints/delete_endpoint.php
.. refdoc:: OpenStack/Identity/v3/Models/Endpoint.html#method_delete
