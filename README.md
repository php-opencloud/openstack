# PHP OpenStack SDK

[![Build Status](https://scrutinizer-ci.com/g/php-opencloud/openstack-prototype-v3/badges/build.png?b=master)](https://scrutinizer-ci.com/g/php-opencloud/openstack-prototype-v3/build-status/master)
[![Code Coverage](https://scrutinizer-ci.com/g/php-opencloud/openstack-prototype-v3/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/php-opencloud/openstack-prototype-v3/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/php-opencloud/openstack-prototype-v3/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/php-opencloud/openstack-prototype-v3/?branch=master)

php-opencloud/openstack is an SDK which allows PHP developers to easily connect to OpenStack APIs in a simple and 
idiomatic way. This binding is specifically designed for OpenStack APIs, but other provider SDKs are available. Multiple 
OpenStack services, and versions of services, are supported.

## Links

* [Official documentation](http://docs.php-opencloud.com)
* [Homepage](http://php-opencloud.com/)
* [Developer support](https://developer.rackspace.com/)
* [Mailing list](https://groups.google.com/forum/#!forum/php-opencloud)
* [Contributing guide](/CONTRIBUTING.md)

## Requirements

* PHP 5.5
* cURL extension

## How to install

```bash
composer require php-opencloud/openstack
```

This will automatically add the following lines to your local `composer.json` file:

```json
{
    "require": {
        "php-opencloud/openstack": "X.Y.Z"
    }
}
```

where `X.Y.Z` is the most recent release version. For a more comprehensive installation guide, please consult our 
[official documentation]().

## Help and feedback

If you're struggling with something or have spotted a potential bug, feel free to submit an issue to our 
[bug tracker](https://github.com/php-opencloud/openstack/issues). 

For general feedback and support requests, contact us on the 
[Rackspace Developer portal](https://developer.rackspace.com/support/).

## Contributing

Engaging the community and lowering barriers for contributors is something we care a lot about. For this reason, we've 
taken the time to write a [contributing guide](CONTRIBUTING.md) for folks interested in getting involved in our project. 
If you're not sure how you can get involved, feel free to 
[submit an issue](https://github.com/php-opencloud/openstack/issues/new) or 
[contact us](https://developer.rackspace.com/support/). You don't need to be a PHP expert - all members of the 
community are welcome!