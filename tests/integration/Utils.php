<?php

namespace OpenStack\Integration;

use GuzzleHttp\Client;
use OpenStack\Identity\v2\Api;
use OpenStack\Identity\v2\Service;
use OpenStack\Common\Transport\HandlerStack;

class Utils
{
    public static function getAuthOptsV3()
    {
        return [
            'authUrl' => getenv('OS_AUTH_URL'),
            'region'  => getenv('OS_REGION_NAME'),
            'user'    => [
                'id'       => getenv('OS_USER_ID'),
                'password' => getenv('OS_PASSWORD'),
            ],
            'scope'   => [
                'project' => [
                    'id' => getenv('OS_PROJECT_ID'),
                ]
            ]
        ];
    }

    public static function getAuthOptsV2()
    {
        $authUrl = \OpenStack\Common\Transport\Utils::normalizeUrl(getenv('OS_AUTH_URL'));
        $httpClient = new Client([
            'base_uri' => $authUrl,
            'handler'  => HandlerStack::create(),
        ]);
        $identityService = new Service($httpClient, new Api);
        return [
            'authUrl'         => $authUrl,
            'region'          => getenv('OS_REGION_NAME'),
            'username'        => getenv('OS_USERNAME'),
            'password'        => getenv('OS_PASSWORD'),
            'tenantName'      => getenv('OS_TENANT_NAME'),
            'identityService' => $identityService,
        ];
    }

    public static function getAuthOpts($options = array())
    {
        $auth_options = getenv('OS_IDENTITY_API_VERSION') == '2.0' ?
            self::getAuthOptsV2() : self::getAuthOptsV3();
        return array_merge($auth_options, $options);
    }
}
