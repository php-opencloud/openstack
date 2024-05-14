<?php

declare(strict_types=1);

namespace OpenStack\PublicDns\v2;

use OpenStack\Common\Api\AbstractParams;

class Params extends AbstractParams
{
    public function tenant(): array
    {
        return [
            'type'        => self::STRING_TYPE,
            'location'    => self::QUERY,
        ];
    }

    public function uuid(): array
    {
        return [
            'type'        => self::STRING_TYPE,
            'location'    => self::QUERY,
        ];
    }

}
