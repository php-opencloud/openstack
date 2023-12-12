# PHP OpenStack SDK


![Unit tests](https://github.com/php-opencloud/openstack/actions/workflows/unit_tests.yml/badge.svg)
[![Documentation Status](https://readthedocs.org/projects/php-openstack-sdk/badge/?version=latest)](https://php-openstack-sdk.readthedocs.io/en/latest/?badge=latest)

[![Block Storage Integration Tests](https://github.com/php-opencloud/openstack/actions/workflows/integration_block_storage.yml/badge.svg)](https://github.com/php-opencloud/openstack/actions/workflows/integration_block_storage.yml)
[![Compute Integration Tests](https://github.com/php-opencloud/openstack/actions/workflows/integration_compute.yml/badge.svg)](https://github.com/php-opencloud/openstack/actions/workflows/integration_compute.yml)
[![Identity Integration Tests](https://github.com/php-opencloud/openstack/actions/workflows/integration_identity.yml/badge.svg)](https://github.com/php-opencloud/openstack/actions/workflows/integration_identity.yml)
[![Images Integration Tests](https://github.com/php-opencloud/openstack/actions/workflows/integration_images.yml/badge.svg)](https://github.com/php-opencloud/openstack/actions/workflows/integration_images.yml)
[![Networking Integration Tests](https://github.com/php-opencloud/openstack/actions/workflows/integration_networking.yml/badge.svg)](https://github.com/php-opencloud/openstack/actions/workflows/integration_networking.yml)
[![Object Storage Integration Tests](https://github.com/php-opencloud/openstack/actions/workflows/integration_object_storage.yml/badge.svg)](https://github.com/php-opencloud/openstack/actions/workflows/integration_object_storage.yml)

`php-opencloud/openstack` is an SDK which allows PHP developers to easily connect to OpenStack APIs in a simple and 
idiomatic way. This binding is specifically designed for OpenStack APIs, but other provider SDKs are available. Multiple 
OpenStack services, and versions of services, are supported.
 
## Links

* [Official documentation](https://php-openstack-sdk.readthedocs.io/en/latest/)
* [Reference documentation](http://refdocs.os.php-opencloud.com)
* [Contributing guide](/CONTRIBUTING.md)
* [Code of Conduct](/CODE_OF_CONDUCT.md)


## We need your help :smiley: 

We invest a large amount of work to ensure this SDK works with many OpenStack distributions via running end-to-end 
integration tests with a real cluster.

If you or your organization are in a position that can help us access popular distributions as listed below, do reach 
out by open an issue in github.

| Distribution                       |                         |
|------------------------------------|-------------------------|
|OpenStack RDO<br>MicroStack Openstack | Sponsored by [![Ai.net](https://i.imgur.com/wsFRFuX.png)](https://www.ai.net/) |
|Red Hat OpenStack                   | Need sponsor!           |
|OVH OpenStack                       | Need sponsor!           |
|SUSE OpenStack                      | Need sponsor!           |
|RackSpace OpenStack                 | Need sponsor!           |

## Join the community
   
- Report an issue: https://github.com/php-opencloud/openstack/issues

## Version Guidance

| Version   | Status                      | PHP Version      | Support until           |
| --------- | --------------------------- | ---------------- | ----------------------- |
| `^3.2`    | Latest                      | `>=7.2.5, >=8.0` | Current                 |
| `^3.1`    | Latest                      | `>=7.2.5`        | Current                 |
| `^3.0`    | Bug fixed only              | `>=7.0`          | Oct 2020                |
| `^2.0`    | End of life                 | `>=7.0,<7.2`     | March 2018              |


## Upgrade from 2.x to 3.x

Due to new [object typehint](https://wiki.php.net/rfc/object-typehint) since PHP 7.2, `Object` is a reserved keyword 
thus class `OpenStack\ObjectStore\v1\Models\Object` had to be renamed to 
`OpenStack\ObjectStore\v1\Models\StorageObject`. 

This change was introduced in [#184](https://github.com/php-opencloud/openstack/pull/184).

## Requirements

* PHP >= 7.2.5
* `ext-curl`

## How to install

```bash
composer require php-opencloud/openstack
```

## Contributing

Engaging the community and lowering barriers for contributors is something we care a lot about. For this reason, we've 
taken the time to write a [contributing guide](CONTRIBUTING.md) for folks interested in getting involved in our project. 
If you're not sure how you can get involved, feel free to 
[submit an issue](https://github.com/php-opencloud/openstack/issues/new). You don't need to be a PHP expert - all members of the 
community are welcome!
