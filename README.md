# PHP OpenStack SDK

[![Build Status](https://travis-ci.org/php-opencloud/openstack.svg?branch=master)](https://travis-ci.org/php-opencloud/openstack)
[![Coverage Status](https://coveralls.io/repos/github/php-opencloud/openstack/badge.svg?branch=master)](https://coveralls.io/github/php-opencloud/openstack?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/php-opencloud/openstack/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/php-opencloud/openstack/?branch=master)

`php-opencloud/openstack` is an SDK which allows PHP developers to easily connect to OpenStack APIs in a simple and 
idiomatic way. This binding is specifically designed for OpenStack APIs, but other provider SDKs are available. Multiple 
OpenStack services, and versions of services, are supported.

## Links

* [Official documentation](https://php-openstack-sdk.readthedocs.io/en/latest/)
* [Reference documentation](http://refdocs.os.php-opencloud.com)
* [Contributing guide](/CONTRIBUTING.md)
* [Code of Conduct](/CODE_OF_CONDUCT.md)

## Getting help
   
- Meet us on Slack: https://phpopencloud.slack.com ([Get your invitation](https://launchpass.com/phpopencloud))
- Report an issue: https://github.com/php-opencloud/openstack/issues


## Version Guidance

| Version   | Status                      | PHP Version   | End of life             |
| --------- | --------------------------- | ------------- | ----------------------- |
| `^3.0`    | Latest                      | `>=7.0`       | March 2020              |
| `^2.0`    | Maintained (Bug fixes only) | `>=7.0,<7.2`  | March 2018              |

## Upgrade from 2.x to 3.x

Due to new [object typehint](https://wiki.php.net/rfc/object-typehint) since PHP 7.2, `Object` is a reserved keyword 
thus class `OpenStack\ObjectStore\v1\Models\Object` had to be renamed to 
`OpenStack\ObjectStore\v1\Models\StorageObject`. 

This change was introduced in [#184](https://github.com/php-opencloud/openstack/pull/184).

## Requirements

* PHP 7.0
* `ext-curl`

## How to install

```bash
composer require php-opencloud/openstack
```

## Contributing

Engaging the community and lowering barriers for contributors is something we care a lot about. For this reason, we've 
taken the time to write a [contributing guide](CONTRIBUTING.md) for folks interested in getting involved in our project. 
If you're not sure how you can get involved, feel free to 
[submit an issue](https://github.com/php-opencloud/openstack/issues/new) or 
[contact us](https://developer.rackspace.com/support/). You don't need to be a PHP expert - all members of the 
community are welcome!