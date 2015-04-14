<?php

namespace OpenStack\Common\Error;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Event\ErrorEvent;
use GuzzleHttp\Event\SubscriberInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Message\RequestInterface;
use GuzzleHttp\Message\ResponseInterface;

class Builder implements SubscriberInterface
{
    private $docDomain = 'http://docs.php-opencloud.com/en/latest/';
    private $client;

    public function __construct(ClientInterface $client = null)
    {
        $this->client = $client ?: new Client();
    }

    /**
     * @codeCoverageIgnore
     * @return array
     */
    public function getEvents()
    {
        return [
            'error' => ['onHttpError']
        ];
    }

    public function onHttpError(ErrorEvent $event)
    {
        throw $this->httpError($event->getRequest(), $event->getResponse());
    }

    private function header($name)
    {
        return sprintf("%s\n%s\n", $name, str_repeat('~', strlen($name)));
    }

    private function linkIsValid($link)
    {
        $link = $this->docDomain . $link;

        try {
            $resp = $this->client->head($link);
        } catch (ClientException $e) {}

        return $resp->getStatusCode() < 400;
    }

    public function httpError(RequestInterface $request, ResponseInterface $response)
    {
        $message = $this->header('HTTP Error');
        
        $message .= sprintf("The remote server returned a \"%d %s\" error for the following transaction:\n\n",
            $response->getStatusCode(), $response->getReasonPhrase());

        $message .= $this->header('Request');
        $message .= trim((string) $request) . PHP_EOL . PHP_EOL;

        $message .= $this->header('Response');
        $message .= trim((string) $response) . PHP_EOL . PHP_EOL;

        $message .= $this->header('Further information');

        // @codeCoverageIgnoreStart
        switch ($response->getStatusCode()) {
            case 400:
                $message .= "Please ensure that your input values are valid and well-formed. ";
                break;
            case 401:
                $message .= "Please ensure that your authentication credentials are valid. ";
                break;
            case 404:
                $message .= "Please ensure that the resource you're trying to access actually exists. ";
                break;
            case 500:
                $message .= "Please try this operation again once you know the remote server is operational. ";
                break;
        }
        // @codeCoverageIgnoreEnd

        $message .= "Visit http://docs.php-opencloud.com/en/latest/http-codes for more information about debugging "
            . "HTTP status codes, or file a support issue on https://github.com/php-opencloud/openstack/issues.";

        return new BadResponseError($message);
    }

    public function userInputError($expectedType, $userValue, $furtherLink = null)
    {
        $message = $this->header('User Input Error');

        $message .= sprintf("%s was expected, but the following value was passed in:\n\n%s\n",
            $expectedType, print_r($userValue, true));

        $message .= "Please ensure that the value adheres to the expectation above. ";

        if ($furtherLink && $this->linkIsValid($furtherLink)) {
            $message .= sprintf("Visit %s for more information about input arguments. ", $this->docDomain . $furtherLink);
        }

        $message .= 'If you run into trouble, please open a support issue on https://github.com/php-opencloud/openstack/issues.';

        return new UserInputError($message);
    }
}
