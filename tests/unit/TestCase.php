<?php

namespace OpenStack\Test;

use function GuzzleHttp\Psr7\stream_for;
use function GuzzleHttp\Psr7\parse_response;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Response;
use Prophecy\Argument;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    /** @var \Prophecy\Prophecy\ObjectProphecy */
    protected $client;

    /** @var string */
    protected $rootFixturesDir;

    protected $api;

    protected function setUp()
    {
        $this->client = $this->prophesize(ClientInterface::class);
    }

    protected function createResponse($status, array $headers, array $json)
    {
        return new Response($status, $headers, stream_for(json_encode($json)));
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

        return parse_response(file_get_contents($path));
    }

    protected function setupMock($method, $path, $body = null, array $headers = [], $response)
    {
        $options = ['headers' => $headers];

        if (!empty($body)) {
            $options[is_array($body) ? 'json' : 'body'] = $body;
        }

        if (is_string($response)) {
            $response = $this->getFixture($response);
        }

        $this->client
            ->request($method, $path, $options)
            ->shouldBeCalled()
            ->willReturn($response);
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

        $this->setupMock('GET', $urlPath, null, [], $responseFile);

        $resources = call_user_func($call);

        $this->assertInstanceOf('\Generator', $resources);

        $count = 0;

        foreach ($resources as $resource) {
            $this->assertInstanceOf('OpenStack\Identity\v3\Models\\' . ucfirst($modelName), $resource);
            ++$count;
        }

        $this->assertEquals(2, $count);
    }

    protected function getTest(callable $call, $modelName)
    {
        $resource = call_user_func($call);

        $this->assertInstanceOf('OpenStack\Identity\v3\Models\\' . ucfirst($modelName), $resource);
        $this->assertEquals('id', $resource->id);
    }
}
