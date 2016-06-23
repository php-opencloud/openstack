<?php

namespace OpenStack\Test\Common\Transport;

use GuzzleHttp\Psr7\Response;
use function GuzzleHttp\Psr7\uri_for;
use GuzzleHttp\Psr7\Uri;
use OpenStack\Common\Transport\Utils;
use OpenStack\Test\TestCase;

class UtilsTest extends TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_decoding_malformed_json_throws_error()
    {
        $response = new Response(200, [], \GuzzleHttp\Psr7\stream_for('{'));

        Utils::jsonDecode($response);
    }

    public function test_it_adds_paths()
    {
        $uri = Utils::addPaths(uri_for('http://openstack.org/foo'), 'bar', 'baz', '1', '2');

        $this->assertInstanceOf(Uri::class, $uri);
        $this->assertEquals(uri_for('http://openstack.org/foo/bar/baz/1/2'), $uri);
    }
}
