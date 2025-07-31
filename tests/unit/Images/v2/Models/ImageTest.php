<?php

namespace OpenStack\Test\Images\v2\Models;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Stream;
use GuzzleHttp\Psr7\Uri;
use OpenStack\Images\v2\Api;
use OpenStack\Images\v2\Models\Member;
use OpenStack\Images\v2\Models\Image;
use OpenStack\Test\TestCase;

class ImageTest extends TestCase
{
    /** @var Image */
    private $image;

    private $path;

    public function setUp(): void
    {
        parent::setUp();

        $this->rootFixturesDir = dirname(__DIR__);

        $this->image = new Image($this->client->reveal(), new Api());
        $this->image->id = '386f0425-3ee8-4688-b73f-272328fe4c71';
        $this->path = sprintf("v2/images/%s", $this->image->id);
    }

    public function test_it_retrieves()
    {
        $returnedUri = function_exists('\GuzzleHttp\Psr7\uri_for')
            ? \GuzzleHttp\Psr7\uri_for('')
            : \GuzzleHttp\Psr7\Utils::uriFor('');

        $this->client->getConfig('base_uri')->shouldBeCalled()->willReturn($returnedUri);

        $this->mockRequest('GET', $this->path, 'GET_image', null, []);

        $this->image->retrieve();

        self::assertEquals("active", $this->image->status);
        self::assertEquals("foo", $this->image->name);
        self::assertEquals([], $this->image->tags);
        self::assertEquals("ami", $this->image->containerFormat);
        self::assertEquals(new \DateTimeImmutable("2015-11-12T14:26:08+0000"), $this->image->createdAt);
        self::assertEquals("ami", $this->image->diskFormat);
        self::assertEquals(new \DateTimeImmutable("2015-12-01T12:25:42+0000"), $this->image->updatedAt);
        self::assertEquals("public", $this->image->visibility);
        self::assertEquals(20, $this->image->minDisk);
        self::assertFalse($this->image->protected);
        self::assertEquals("386f0425-3ee8-4688-b73f-272328fe4c71", $this->image->id);
        self::assertEquals("061d01418b94d4743a98ee26d941e87c", $this->image->checksum);
        self::assertEquals("057aad9fa85b4e29b23e7888000446ef", $this->image->ownerId);
        self::assertEquals(983040, $this->image->size);
        self::assertEquals(0, $this->image->minRam);
        self::assertNull($this->image->virtualSize);
    }

    public function test_it_updates()
    {
        $this->client->getConfig('base_uri')->shouldBeCalled()->willReturn(new Uri);

        $opts = [
            'minDisk'         => 1,
            'minRam'          => 1,
            'name'            => 'newName',
            'owner'           => 'bar',
            'protected'       => true,
            'tags'            => ['1', '2', '3'],
            'visibility'      => 'private',
        ];

        $expectedJson = json_encode([
            (object) ['op' => 'replace', 'path' => '/tags', 'value' => $opts['tags']],
            (object) ['op' => 'replace', 'path' => '/min_ram', 'value' => $opts['minRam']],
            (object) ['op' => 'replace', 'path' => '/visibility', 'value' => $opts['visibility']],
            (object) ['op' => 'replace', 'path' => '/owner', 'value' => $opts['owner']],
            (object) ['op' => 'replace', 'path' => '/min_disk', 'value' => $opts['minDisk']],
            (object) ['op' => 'replace', 'path' => '/name', 'value' => $opts['name']],
            (object) ['op' => 'replace', 'path' => '/protected', 'value' => $opts['protected']],
        ], JSON_UNESCAPED_SLASHES);

        $this->mockRequest('GET', $this->path, 'GET_image', null, []);
        $this->mockRequest('GET', 'v2/schemas/image', 'GET_image_schema', null, []);

        $headers = ['Content-Type' => 'application/openstack-images-v2.1-json-patch'];
        $this->mockRequest('PATCH', $this->path, 'POST_image', $expectedJson, $headers);

        $this->image->update($opts);
    }

    public function test_it_throws_exception_if_user_input_does_not_match_schema()
    {
        $this->client->getConfig('base_uri')->shouldBeCalled()->willReturn(new Uri);

        $this->mockRequest('GET', $this->path, 'GET_image', null, []);
        $this->mockRequest('GET', 'v2/schemas/image', 'GET_image_schema', null, []);
        $this->expectException(\Exception::class);

        $this->image->update([
            'minDisk' => 'foo',
        ]);
    }

    public function test_it_deletes()
    {
        $this->mockRequest('DELETE', $this->path, new Response(204), null, []);

        $this->image->delete();
    }

    public function test_it_reactivates()
    {
        $this->mockRequest('POST', $this->path . '/actions/reactivate', new Response(204), null, []);

        $this->image->reactivate();
    }

    public function test_it_deactivates()
    {
        $this->mockRequest('POST', $this->path . '/actions/deactivate', new Response(204), null, []);

        $this->image->deactivate();
    }

    public function test_it_uploads_data_stream()
    {
        $stream = function_exists('\GuzzleHttp\Psr7\stream_for')
            ? \GuzzleHttp\Psr7\stream_for('data')
            : \GuzzleHttp\Psr7\Utils::streamFor('data');

        $headers = ['Content-Type' => 'application/octet-stream'];

        $this->mockRequest('PUT', $this->path . '/file', new Response(204), $stream, $headers);

        $this->image->uploadData($stream);
    }

    public function test_it_downloads_data()
    {
        $stream = function_exists('\GuzzleHttp\Psr7\stream_for')
            ? \GuzzleHttp\Psr7\stream_for('data')
            : \GuzzleHttp\Psr7\Utils::streamFor('data');

        $headers = ['Content-Type' => 'application/octet-stream'];
        $response = new Response(200, $headers, $stream);

        $this->mockRequest('GET', $this->path . '/file', $response, null, []);

        self::assertInstanceOf(Stream::class, $this->image->downloadData());
    }

    public function test_it_creates_member()
    {
        $memberId = '8989447062e04a818baf9e073fd04fa7';
        $expectedJson = ['member' => $memberId];

        $this->mockRequest('POST', $this->path . '/members', 'POST_members', $expectedJson);

        $member = $this->image->addMember('8989447062e04a818baf9e073fd04fa7');
        self::assertInstanceOf(Member::class, $member);
    }

    public function test_it_lists_members()
    {
        $this->mockRequest('GET', $this->path . '/members', 'GET_members');

        $count = 0;

        foreach ($this->image->listMembers() as $member) {
            ++$count;
            self::assertInstanceOf(Member::class, $member);
        }

        self::assertEquals(2, $count);
    }

    public function test_it_gets_members()
    {
        self::assertInstanceOf(Member::class, $this->image->getMember('id'));
    }
}
