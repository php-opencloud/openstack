<?php

namespace OpenStack\Networking\v2;

use OpenStack\Common\Api\AbstractParams;

class Params extends AbstractParams
{
    public function urlId($type)
    {
        return array_merge(parent::id($type), [
            'required' => true,
            'location' => self::URL,
        ]);
    }

    public function shared()
    {
        return [
            'type' => self::BOOL_TYPE,
            'location' => self::JSON,
            'description' => 'Indicates whether this network is shared across all tenants',
       ];
    }

    public function adminStateUp()
    {
        return [
            'type' => self::BOOL_TYPE,
            'location' => self::JSON,
            'sentAs' => 'admin_state_up',
            'description' => 'The administrative state of the network',
        ];
    }

    public function networkId()
    {
        return [
            'type'        => self::STRING_TYPE,
            'required'    => true,
            'sentAs'      => 'network_id',
            'description' => 'The ID of the attached network',
        ];
    }

    public function ipVersion()
    {
        return [
            'type'        => self::INT_TYPE,
            'required'    => true,
            'sentAs'      => 'ip_version',
            'description' => 'The IP version, which is 4 or 6',
        ];
    }

    public function cidr()
    {
        return [
            'type'        => self::STRING_TYPE,
            'required'    => true,
            'sentAs'      => 'cidr',
            'description' => 'The CIDR',
        ];
    }

    public function tenantId()
    {
        return [
            'type'        => self::STRING_TYPE,
            'sentAs'      => 'tenant_id',
            'description' => <<<EOT
The ID of the tenant who owns the network. Only administrative users can specify a tenant ID other than their own.
You cannot change this value through authorization policies
EOT
        ];
    }

    public function gatewayIp()
    {
        return [
            'type'        => self::STRING_TYPE,
            'sentAs'      => 'gateway_ip',
            'description' => 'The gateway IP address',
        ];
    }

    public function enableDhcp()
    {
        return [
            'type'        => self::BOOL_TYPE,
            'sentAs'      => 'enable_dhcp',
            'description' => 'Set to true if DHCP is enabled and false if DHCP is disabled.',
        ];
    }

    public function dnsNameservers()
    {
        return [
            'type'        => self::STRING_TYPE,
            'sentAs'      => 'dns_nameservers',
            'description' => 'A list of DNS name servers for the subnet.',
          ];
    }

    public function allocationPools()
    {
        return [
            'type'        => self::ARRAY_TYPE,
            'sentAs'      => 'allocation_pools',
            'items'       => [
                'type'       => self::OBJECT_TYPE,
                'properties' => [
                    'start'     => [
                        'type'        => self::STRING_TYPE,
                        'description' => 'The start address for the allocation pools',
                    ],
                    'end' => [
                        'type'        => self::STRING_TYPE,
                        'description' => 'The end address for the allocation pools',
                    ],
                ]
            ],
            'description' => 'The start and end addresses for the allocation pools',
        ];
    }

    public function hostRoutes()
    {
        return [
            'type'        => self::ARRAY_TYPE,
            'sentAs'      => 'host_routes',
            'items'       => [
                'type'       => self::OBJECT_TYPE,
                'properties' => [
                    'destination'     => [
                        'type'        => self::STRING_TYPE,
                        'description' => 'Destination for static route',
                    ],
                    'nexthop' => [
                        'type'        => self::STRING_TYPE,
                        'description' => 'Nexthop for the destination',
                    ],
                ]
            ],
            'description' => 'A list of host route dictionaries for the subnet',
        ];
    }
}
