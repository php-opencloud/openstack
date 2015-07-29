<?php

namespace OpenStack\Common\Error;

use GuzzleHttp\Message\RequestInterface;
use GuzzleHttp\Message\ResponseInterface;

/**
 * Represents a HTTP-specific error, caused by 4xx or 5xx response statuses.
 *
 * @package OpenStack\Common\Error
 */
class BadResponseError extends BaseError
{
    /** @var RequestInterface */
    public $request;

    /** @var ResponseInterface */
    public $response;
}