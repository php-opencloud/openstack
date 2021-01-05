<?php

namespace OpenStack\Test\Common\Transport;

use GuzzleHttp\Psr7\Response;
use function GuzzleHttp\Psr7\uri_for;
use GuzzleHttp\Psr7\Uri;
use OpenStack\Common\Transport\Utils;
use OpenStack\Test\TestCase;

class UtilsTest extends TestCase
{
    public function test_decoding_malformed_json_throws_error()
    {
        $response = new Response(200, [], \GuzzleHttp\Psr7\stream_for('{'));
		$this->expectException(\InvalidArgumentException::class);

        Utils::jsonDecode($response);
    }

    public function test_it_adds_paths()
    {
        $uri = Utils::addPaths(uri_for('http://openstack.org/foo'), 'bar', 'baz', '1', '2');

        self::assertInstanceOf(Uri::class, $uri);
        self::assertEquals(uri_for('http://openstack.org/foo/bar/baz/1/2'), $uri);
    }
}
