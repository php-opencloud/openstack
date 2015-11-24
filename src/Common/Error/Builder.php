<?php

namespace OpenStack\Common\Error;

use function GuzzleHttp\Psr7\str;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class responsible for building meaningful exceptions. For HTTP problems, it produces a {@see HttpError}
 * exception, and supplies a error message with reasonable defaults. For user input problems, it produces a
 * {@see UserInputError} exception. For both, the problem is described, a potential solution is offered and
 * a link to further information is included.
 *
 * @package OpenStack\Common\Error
 */
class Builder
{
    /**
     * The default domain to use for further link documentation.
     *
     * @var string
     */
    private $docDomain = 'http://docs.php-opencloud.com/en/latest/';

    /**
     * The HTTP client required to validate the further links.
     *
     * @var Client
     */
    private $client;

    /**
     * @param ClientInterface $client
     */
    public function __construct(ClientInterface $client = null)
    {
        $this->client = $client ?: new Client();
    }

    /**
     * Internal method used when outputting headers in the error description.
     *
     * @param $name
     *
     * @return string
     */
    private function header($name)
    {
        return sprintf("%s\n%s\n", $name, str_repeat('~', strlen($name)));
    }

    /**
     * Before outputting custom links, it is validated to ensure that the user is not
     * directed off to a broken link. If a 404 is detected, it is hidden.
     *
     * @param $link The proposed link
     *
     * @return bool
     */
    private function linkIsValid($link)
    {
        $link = $this->docDomain . $link;

        try {
            $resp = $this->client->request('HEAD', $link);
        } catch (ClientException $e) {
        }

        return $resp->getStatusCode() < 400;
    }

    /**
     * Helper method responsible for constructing and returning {@see BadResponseError} exceptions.
     *
     * @param RequestInterface  $request  The faulty request
     * @param ResponseInterface $response The error-filled response
     *
     * @return BadResponseError
     */
    public function httpError(RequestInterface $request, ResponseInterface $response)
    {
        $message = $this->header('HTTP Error');

        $message .= sprintf("The remote server returned a \"%d %s\" error for the following transaction:\n\n",
            $response->getStatusCode(), $response->getReasonPhrase());

        $message .= $this->header('Request');
        $message .= trim(str($request)) . PHP_EOL . PHP_EOL;

        $message .= $this->header('Response');
        $message .= trim(str($response)) . PHP_EOL . PHP_EOL;

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

        $e = new BadResponseError($message);
        $e->setRequest($request);
        $e->setResponse($response);

        return $e;
    }

    /**
     * Helper method responsible for constructing and returning {@see UserInputError} exceptions.
     *
     * @param string      $expectedType The type that was expected from the user
     * @param mixed       $userValue    The incorrect value the user actually provided
     * @param string|null $furtherLink  A link to further information if necessary (optional).
     *
     * @return UserInputError
     */
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
