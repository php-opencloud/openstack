<?php

namespace OpenStack\Test;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Message;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Utils;
use Prophecy\Prophecy\MethodProphecy;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    /** @var \Prophecy\Prophecy\ObjectProphecy<\GuzzleHttp\ClientInterface> */
    protected $client;

    /** @var string */
    protected $rootFixturesDir;

    protected $api;

    protected function setUp(): void
    {
        $this->client = $this->prophesize(ClientInterface::class);
    }

    protected function createResponse($status, array $headers, array $json)
    {
        return new Response($status, $headers, Utils::streamFor(json_encode($json)));
    }

    protected function getFixture($file)
    {
        if (!$this->rootFixturesDir) {
            throw new \RuntimeException('Root fixtures dir not set');
        }

        $path = $this->rootFixturesDir . '/Fixtures/' . $file . '.resp';

        if (!file_exists($path)) {
            throw new \RuntimeException(sprintf("%s does not exist", $path));
        }

        return Message::parseResponse(file_get_contents($path));
    }


    /**
     * Mocks request
     *
     * @param string $method method of request: GET, POST, PUT, DELETE
     * @param string|array $uri request path or array with path and query
     * @param string|\GuzzleHttp\Psr7\Response|\Throwable $response the file name of the response fixture or a Response object
     * @param string|array|null $body request body. If type is array, it will be encoded as JSON.
     * @param array $headers request headers
     * @param bool $skipAuth true if the api call skips authentication
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function mockRequest(string $method, $uri, $response = null, $body = null, array $headers = [], $skipAuth = false): MethodProphecy
    {
        $options = [
            'headers'             => $headers,
            'openstack.skip_auth' => $skipAuth,
        ];

        if (!empty($body)) {
            $options[is_array($body) ? 'json' : 'body'] = $body;
        }


        if (is_string($uri)) {
            $uri = ['path' => $uri];
        }

        if (isset($uri['query'])) {
            $options['query'] = $uri['query'];
        }

        $method = $this->client
            ->request($method, $uri['path'] ?? '', $options)
            ->shouldBeCalled();

        if (is_string($response)) {
            $method = $method->willReturn($this->getFixture($response));
        } elseif ($response instanceof Response) {
            $method = $method->willReturn($response);
        } elseif ($response instanceof \Throwable) {
            $method = $method->willThrow($response);
        } else {
            throw new \InvalidArgumentException('Response must be either a string, a Response object or an instance of Throwable');
        }

        return $method;
    }

    protected function createFn($receiver, $method, $args)
    {
        return function () use ($receiver, $method, $args) {
            return $receiver->$method($args);
        };
    }

    protected function listTest(callable $call, $urlPath, $modelName = null, $responseFile = null)
    {
        $modelName = $modelName ?: $urlPath;
        $responseFile = $responseFile ?: $urlPath;

        $this->mockRequest('GET', $urlPath, $responseFile, null, []);

        $resources = call_user_func($call);

        self::assertInstanceOf('\Generator', $resources);

        $count = 0;

        foreach ($resources as $resource) {
            self::assertInstanceOf('OpenStack\Identity\v3\Models\\' . ucfirst($modelName), $resource);
            ++$count;
        }

        self::assertEquals(2, $count);
    }

    protected function getTest(callable $call, $modelName)
    {
        $resource = call_user_func($call);

        self::assertInstanceOf('OpenStack\Identity\v3\Models\\' . ucfirst($modelName), $resource);
        self::assertEquals('id', $resource->id);
    }
}
