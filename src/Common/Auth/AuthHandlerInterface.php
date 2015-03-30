<?php

namespace OpenStack\Common\Auth;

use GuzzleHttp\Event\BeforeEvent;
use GuzzleHttp\Event\SubscriberInterface;

/**
 * This interface will eventually be used to unite common functionality between different providers'
 * authentication strategies. If this library is ever fully multi-provider, we will need to extract
 * the OpenStack-specific nomenclature like tokens - but this can definitely wait.
 *
 * @package OpenStack\Common\Auth
 */
interface AuthHandlerInterface extends SubscriberInterface
{
    /**
     * Used to check the current token and re-authenticate if necessary.
     *
     * @param BeforeEvent $event
     * @return mixed
     */
    public function checkTokenIsValid(BeforeEvent $event);

    /**
     * Generate a new token and save for future use.
     *
     * @return mixed
     */
    public function authenticate();
}