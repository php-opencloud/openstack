<?php

namespace OpenStack\Test\Common\Error;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\NoSeekStream;
use GuzzleHttp\Psr7\PumpStream;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Utils;
use OpenStack\Common\Error\BadResponseError;
use OpenStack\Common\Error\Builder;
use OpenStack\Common\Error\UserInputError;

class BuilderTest extends \PHPUnit\Framework\TestCase
{
    private $builder;
    private $client;

    public function setUp(): void
    {
        $this->client = $this->prophesize(ClientInterface::class);
        $this->builder = new Builder($this->client->reveal());
    }

    public function test_it_injects_client()
    {
        self::assertInstanceOf(Builder::class, new Builder($this->client->reveal()));
    }

    /**
     * @dataProvider verbosityProvider
     */
    public function test_it_builds_http_errors(int $verbosity)
    {
        $request = new Request('POST', '/servers');
        $response = new Response(400, [], Utils::streamFor('Invalid parameters'));

        $requestStr = $this->builder->str($request, $verbosity);
        $responseStr = $this->builder->str($response, $verbosity);

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

        self::assertEquals($e, $this->builder->httpError($request, $response, $verbosity));
    }

    /**
     * Provides different verbosity levels.
     */
    public function verbosityProvider(): array
    {
        return [
            [0],
            [1],
            [2],
        ];
    }

    public function test_it_outputs_body_for_json()
    {
        $value = 'foobar';

        $request = new Request(
            'POST',
            '/servers',
            ['Content-Type' => 'application/json'],
            json_encode(['foo' => $value])
        );

        $str = $this->builder->str($request, 2);
        $this->assertStringContainsString($value, $str);
    }

    public function test_it_skips_body_for_low_verbosity()
    {
        $value = 'foobar';

        $request = new Request(
            'POST',
            '/servers',
            ['Content-Type' => 'application/json'],
            json_encode(['foo' => $value])
        );

        $str = $this->builder->str($request, 1);
        $this->assertStringNotContainsString($value, $str);
    }

    public function test_it_cuts_big_body_for_json()
    {
        $value = str_repeat('A', Builder::MAX_BODY_LENGTH);

        $request = new Request(
            'POST',
            '/servers',
            ['Content-Type' => 'application/json'],
            json_encode(['foo' => $value])
        );

        $str = $this->builder->str($request, 2);
        $this->assertStringNotContainsString($value, $str);
        $this->assertStringContainsString('AAAAAA...', $str);
    }

    public function test_it_did_not_read_full_body_for_json()
    {
        $value = str_repeat('A', Builder::MAX_BODY_LENGTH + 1);

        $request = new Request(
            'POST',
            '/servers',
            ['Content-Type' => 'application/json'],
            new PumpStream(function ($size) {
                return str_repeat('A', $size);
            })
        );

        $str = $this->builder->str($request, 2);
        $this->assertStringNotContainsString($value, $str);
        $this->assertStringContainsString('AAAAAA...', $str);
    }

    public function test_it_skips_body_for_binary()
    {
        $value = 'foobar';

        $request = new Request(
            'POST',
            '/servers',
            ['Content-Type' => 'binary/octet-stream'],
            $value
        );

        $str = $this->builder->str($request, 2);
        $this->assertStringNotContainsString($value, $str);
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

        self::assertEquals($e, $this->builder->userInputError($expected, $value, 'index.html'));
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
            ->willThrow(ClientException::class);

        $e = new UserInputError($errorMessage);

        self::assertEquals($e, $this->builder->userInputError($expected, $value, 'sdffsda'));
    }
}
