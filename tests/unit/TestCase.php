<?php

namespace OpenStack\Test;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Event\Emitter;
use GuzzleHttp\Message\MessageFactory;
use GuzzleHttp\Message\Request;
use GuzzleHttp\Message\RequestInterface;
use GuzzleHttp\Message\Response;
use GuzzleHttp\Stream\Stream;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTestCase;

abstract class TestCase extends ProphecyTestCase
{
    /** @var \Prophecy\Prophecy\ObjectProphecy */
    protected $client;

    /** @var string */
    protected $rootFixturesDir;

    protected $api;

    protected function setUp()
    {
        $this->client = $this->prophesize(ClientInterface::class);
        $this->client->getEmitter()->willReturn(new Emitter());
    }

    protected function createResponse($status, array $headers, array $json)
    {
        return new Response($status, $headers, Stream::factory(json_encode($json)));
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

        $contents = file_get_contents($path);

        return (new MessageFactory())->fromMessage($contents);
    }

    protected function setupMockRequest($method, $path, array $json = [], array $headers = [])
    {
        $request = new Request($method, $path, $headers, Stream::factory(json_encode($json)));

        $options = ['exceptions' => false];

        if (!empty($json)) {
            $options['json'] = $json;
        }
        if (!empty($headers)) {
            $options['headers'] = $headers;
        }

        $this->client
            ->createRequest($method, $path, $options)
            ->shouldBeCalled()
            ->willReturn($request);

        return $request;
    }

    protected function setupMockResponse(RequestInterface $request, $response)
    {
        // If a string is passed in, assume its a path to a HTTP representation
        if (is_string($response)) {
            $response = $this->getFixture($response);
        }

        $this->client
            ->send(Argument::is($request))
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

        $request = $this->setupMockRequest('GET', $urlPath);
        $this->setupMockResponse($request, $responseFile);

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