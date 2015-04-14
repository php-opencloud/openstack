<?php

namespace OpenStack\Test\Common\Error;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Event\ErrorEvent;
use GuzzleHttp\Message\Request;
use GuzzleHttp\Message\Response;
use GuzzleHttp\Stream\Stream;
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


        $e = new BadResponseError($errorMessage);

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
            ->head($link)
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
            ->head('http://docs.php-opencloud.com/en/latest/sdffsda')
            ->shouldBeCalled()
            ->willReturn(new Response(404));

        $e = new UserInputError($errorMessage);

        $this->assertEquals($e, $this->builder->userInputError($expected, $value, 'sdffsda'));
    }

    /**
     * @expectedException \OpenStack\Common\Error\BadResponseError
     */
    public function test_an_exception_is_thrown()
    {
        $trans = new Transaction(new Client(), new Request('GET', ''));
        $trans->response = new Response(200);
        $event = new ErrorEvent($trans);

        $this->builder->onHttpError($event);
    }
}
