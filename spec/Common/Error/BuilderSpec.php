<?php

namespace spec\OpenStack\Common\Error;

use GuzzleHttp\Message\Request;
use GuzzleHttp\Message\Response;
use GuzzleHttp\Stream\Stream;
use OpenStack\Common\Error\BadResponseError;
use OpenStack\Common\Error\UserInputError;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class BuilderSpec extends ObjectBehavior
{
    public function getMatchers()
    {
        return [
            'matchException' => function (\Exception $a, \Exception $b) {
                return $a->getMessage() == $b->getMessage();
            }
        ];
    }

    function it_builds_http_errors(BadResponseError $e)
    {
        $request = new Request('POST', '/servers');
        $response = new Response(400, [], Stream::factory('Invalid parameters'));

        $requestStr = trim((string)$request);
        $responseStr = trim((string)$response);

        $errorMessage = <<<EOT
HTTP Error
~~~~~~~~~~
The remote server returned a "400 Bad Request" error for the following transaction:

Request
~~~~~~~
$requestStr

Response
~~~~~~~~
$responseStr

Further information
~~~~~~~~~~~~~~~~~~~
Please ensure that your input values are valid and well-formed. Visit http://docs.php-opencloud.com/en/latest/http-codes for more information about debugging HTTP status codes, or file a support issue on https://github.com/php-opencloud/openstack/issues.
EOT;


        $e->beConstructedWith([$errorMessage]);

        $this->httpError($request, $response)->shouldMatchException($e);
    }

    function it_builds_user_input_errors(UserInputError $e)
    {
        $expected = 'A well-formed string';
        $value = ['foo' => true];

        $errorMessage = <<<EOT
User Input Error
~~~~~~~~~~~~~~~~
A well-formed string was expected, but the following value was passed in:

Array
(
    [foo] => 1
)

Please ensure that the value adheres to the expectation above. If you run into trouble, please open a support issue on https://github.com/php-opencloud/openstack/issues.
EOT;


        $e->beConstructedWith([$errorMessage]);

        $this->userInputError($expected, $value)->shouldMatchException($e);
    }
}
