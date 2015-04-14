<?php

namespace OpenStack\Identity\v2\Api;

class Api
{
    public static function postToken()
    {
        return [
            'method' => 'POST',
            'path'   => 'tokens',
            'params' => [
                'username' => [
                    'type' => 'string',
                    'required' => true,
                    'path' => 'auth.passwordCredentials'
                ],
                'password' => [
                    'type' => 'string',
                    'required' => true,
                    'path' => 'auth.passwordCredentials'
                ],
                'tenantId' => [
                    'type' => 'string',
                    'path' => 'auth',
                ],
                'tenantName' => [
                    'type' => 'string',
                    'path' => 'auth',
                ]
            ],
        ];
    }
} 