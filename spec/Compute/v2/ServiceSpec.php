<?php

namespace spec\OpenStack\Compute\v2;

use GuzzleHttp\Client;
use GuzzleHttp\Message\Response;
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

    function it_creates_a_server()
    {
        $mock = new Mock([new Response(201, [])]);
        $this->client->getEmitter()->attach($mock);

        $opts = [
            'name' => 'foo',
            'imageId' => 'bar',
            'flavorId' => 'baz',
        ];

        $this->createServer($opts)->shouldReturnAnInstanceOf('OpenStack\Compute\v2\Models\Server');
    }


}