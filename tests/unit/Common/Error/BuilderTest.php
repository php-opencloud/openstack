<?php

namespace OpenStack\Test\Common\Error;

use function GuzzleHttp\Psr7\stream_for;
use function GuzzleHttp\Psr7\str;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Transaction;
use OpenStack\Common\Error\BadResponseError;
use OpenStack\Common\Error\Builder;
use OpenStack\Common\Error\UserInputError;

class BuilderTest extends \PHPUnit_Framework_TestCase
{
    private $builder;
    private $client;

    public function __construct()
    {
        $this->client = $this->prophesize(ClientInterface::class);
        $this->builder = new Builder($this->client->reveal());
    }

    public function test_it_builds_http_errors()
    {
        $request = new Request('POST', '/servers');
        $response = new Response(400, [], stream_for('Invalid parameters'));

        $requestStr = trim(str($request));
        $responseStr = trim(str($response));

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

        $e = new BadResponseError($errorMessage);
        $e->setRequest($request);
        $e->setResponse($response);

        $this->assertEquals($e, $this->builder->httpError($request, $response));
    }

    public function test_it_builds_user_input_errors()
    {
        $expected = 'A well-formed string';
        $value = ['foo' => true];
        $link = 'http://docs.php-opencloud.com/en/latest/index.html';

        $errorMessage = <<<EOT
User Input Error
~~~~~~~~~~~~~~~~
A well-formed string was expected, but the following value was passed in:

Array
(
    [foo] => 1
)

Please ensure that the value adheres to the expectation above. Visit $link for more information about input arguments. If you run into trouble, please open a support issue on https://github.com/php-opencloud/openstack/issues.
EOT;

        $this->client
            ->request('HEAD', $link)
            ->shouldBeCalled()
            ->willReturn(new Response(200));

        $e = new UserInputError($errorMessage);

        $this->assertEquals($e, $this->builder->userInputError($expected, $value, 'index.html'));
    }

    public function test_dead_links_are_ignored()
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

        $this->client
            ->request('HEAD', 'http://docs.php-opencloud.com/en/latest/sdffsda')
            ->shouldBeCalled()
            ->willReturn(new Response(404));

        $e = new UserInputError($errorMessage);

        $this->assertEquals($e, $this->builder->userInputError($expected, $value, 'sdffsda'));
    }
}
