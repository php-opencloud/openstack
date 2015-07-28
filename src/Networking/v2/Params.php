<?php

namespace OpenStack\Networking\v2;

use OpenStack\Common\Api\AbstractParams;

class Params extends AbstractParams
{
    public function urlId($type)
    {
        return parent::id($type) + [
            'required' => true,
            'location' => self::URL,
        ];
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

}
