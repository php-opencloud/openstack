<?php

namespace spec\OpenStack\Compute\v2;

use GuzzleHttp\Client;
use GuzzleHttp\Message\Response;
use GuzzleHttp\Stream\Stream;
use GuzzleHttp\Subscriber\Mock;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ServiceSpec extends ObjectBehavior
{
    private $client;

    function let()
    {
        $this->client = new Client();

        $this->beConstructedWith($this->client);
    }

    private function addMockResponse($httpStatus, $body = null, array $headers = [])
    {
        $response = new Response($httpStatus, $headers, Stream::factory($body));
        $mock     = new Mock([$response]);
        $this->client->getEmitter()->attach($mock);
    }

    function it_creates_a_server()
    {
        $this->addMockResponse(201);

        $opts = [
            'name' => 'foo',
            'imageId' => 'bar',
            'flavorId' => 'baz',
        ];

        $this->createServer($opts)->shouldReturnAnInstanceOf('OpenStack\Compute\v2\Models\Server');
    }

    function it_lists_servers()
    {
        $this->addMockResponse(200, Fixtures::getServers());

        foreach ($this->listServers() as $server) {
            $server->
        }
    }

    function it_lists_servers_in_detail()
    {

    }

    function it_lists_flavors()
    {

    }

    function it_lists_flavors_in_detail()
    {

    }

    function it_gets_a_flavor()
    {

    }

    function it_lists_images()
    {

    }

    function it_lists_images_in_detail()
    {

    }

    function it_gets_an_image()
    {

    }
}