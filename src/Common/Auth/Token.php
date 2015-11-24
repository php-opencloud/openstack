<?php

namespace OpenStack\Common\Auth;

interface Token
{
    public function getId();

    /**
     * Indicates whether the token has expired or not.
     *
     * @return bool TRUE if the token has expired, FALSE if it is still valid
     */
    public function hasExpired();
}
