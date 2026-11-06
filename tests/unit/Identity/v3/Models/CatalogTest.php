<?php

namespace OpenStack\Test\Identity\v3\Models;

use OpenStack\Identity\v3\Api;
use OpenStack\Identity\v3\Models\Catalog;
use OpenStack\Identity\v3\Models\Service;
use OpenStack\Test\TestCase;
use Prophecy\Argument;

class CatalogTest extends TestCase
{
    private $catalog;

    public function setUp(): void
    {
        $this->rootFixturesDir = dirname(__DIR__);

        parent::setUp();

        $this->catalog = new Catalog($this->client->reveal(), new Api());
    }

    public function test_it_throws_if_no_services_set()
    {
		$this->expectException(\RuntimeException::class);
        self::assertFalse($this->catalog->getServiceUrl('', '', '', ''));
    }

    public function test_it_returns_service_url()
    {
        $url = 'http://example.org';

        $service = $this->prophesize(Service::class);
        $service->getUrl('foo', 'bar', 'baz', '')->shouldBeCalled()->willReturn($url);

        $this->catalog->services = [$service->reveal()];

        self::assertEquals($url, $this->catalog->getServiceUrl('foo', 'bar', 'baz', ''));
    }

    public function test_it_returns_service_url_override_matching_name_and_type()
    {
        $overrides = [
            ['name' => 'swift', 'type' => 'object-store', 'url' => 'http://override.example'],
        ];

        self::assertEquals(
            'http://override.example',
            $this->catalog->getServiceUrlOverride('swift', 'object-store', 'RegionOne', 'public', $overrides)
        );
    }

    public function test_it_returns_service_url_override_matching_only_name()
    {
        $overrides = [
            ['name' => 'swift', 'url' => 'http://override.example'],
        ];

        self::assertEquals(
            'http://override.example',
            $this->catalog->getServiceUrlOverride('swift', 'object-store', 'RegionOne', 'public', $overrides)
        );
    }

    public function test_it_returns_service_url_override_matching_only_type()
    {
        $overrides = [
            ['type' => 'object-store', 'url' => 'http://override.example'],
        ];

        self::assertEquals(
            'http://override.example',
            $this->catalog->getServiceUrlOverride('swift', 'object-store', 'RegionOne', 'public', $overrides)
        );
    }

    public function test_it_returns_service_url_override_matching_region_and_interface()
    {
        $overrides = [
            [
                'name'      => 'swift',
                'region'    => 'RegionOne',
                'interface' => 'public',
                'url'       => 'http://override.example',
            ],
        ];

        self::assertEquals(
            'http://override.example',
            $this->catalog->getServiceUrlOverride('swift', 'object-store', 'RegionOne', 'public', $overrides)
        );
    }

    public function test_it_returns_null_when_no_override_matches()
    {
        $overrides = [
            ['name' => 'nova', 'url' => 'http://override.example'],
        ];

        self::assertNull(
            $this->catalog->getServiceUrlOverride('swift', 'object-store', 'RegionOne', 'public', $overrides)
        );
    }

    public function test_it_returns_null_when_region_or_interface_does_not_match()
    {
        $overrides = [
            [
                'name'      => 'swift',
                'region'    => 'RegionTwo',
                'interface' => 'internal',
                'url'       => 'http://override.example',
            ],
        ];

        self::assertNull(
            $this->catalog->getServiceUrlOverride('swift', 'object-store', 'RegionOne', 'public', $overrides)
        );
    }

    public function test_it_returns_null_for_empty_overrides()
    {
        self::assertNull(
            $this->catalog->getServiceUrlOverride('swift', 'object-store', 'RegionOne', 'public', [])
        );
    }

    public function test_it_throws_when_matching_override_has_neither_name_nor_type()
    {
        $overrides = [
            ['region' => 'RegionOne', 'url' => 'http://override.example'],
        ];

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('must specify either a "name" or a "type"');

        $this->catalog->getServiceUrlOverride('swift', 'object-store', 'RegionOne', 'public', $overrides);
    }

    public function test_it_throws_when_matching_override_has_no_url()
    {
        $overrides = [
            ['name' => 'swift'],
        ];

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('must specify an "url"');

        $this->catalog->getServiceUrlOverride('swift', 'object-store', 'RegionOne', 'public', $overrides);
    }

    public function test_it_throws_when_invalid_override_does_not_match()
    {
        $overrides = [
            ['name' => 'nova'],
        ];

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('must specify an "url"');

        $this->catalog->getServiceUrlOverride('swift', 'object-store', 'RegionOne', 'public', $overrides);
    }

    public function test_it_throws_if_no_url_found()
    {
        $service = $this->prophesize(Service::class);
        $service->getUrl(Argument::any(), Argument::cetera())->shouldBeCalled()->willReturn(false);
		$this->expectException(\RuntimeException::class);

        $this->catalog->services = [$service->reveal()];

        self::assertFalse($this->catalog->getServiceUrl('', '', '', ''));
    }
}
