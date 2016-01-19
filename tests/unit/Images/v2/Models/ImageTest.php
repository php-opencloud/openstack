<?php

namespace OpensTack\Test\Images\v2\Models;

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

    public function setUp()
    {
        parent::setUp();

        $this->rootFixturesDir = dirname(__DIR__);

        $this->image = new Image($this->client->reveal(), new Api());
        $this->image->id = '386f0425-3ee8-4688-b73f-272328fe4c71';
        $this->path = sprintf("v2/images/%s", $this->image->id);
    }

    public function test_it_retrieves()
    {
        $this->client->getConfig('base_uri')->shouldBeCalled()->willReturn(\GuzzleHttp\Psr7\uri_for(''));

        $this->setupMock('GET', $this->path, null, [], 'GET_image');

        $this->image->retrieve();

        $this->assertEquals("active", $this->image->status);
        $this->assertEquals("foo", $this->image->name);
        $this->assertEquals([], $this->image->tags);
        $this->assertEquals("ami", $this->image->containerFormat);
        $this->assertEquals(new \DateTimeImmutable("2015-11-12T14:26:08+0000"), $this->image->createdAt);
        $this->assertEquals("ami", $this->image->diskFormat);
        $this->assertEquals(new \DateTimeImmutable("2015-12-01T12:25:42+0000"), $this->image->updatedAt);
        $this->assertEquals("public", $this->image->visibility);
        $this->assertEquals(20, $this->image->minDisk);
        $this->assertFalse($this->image->protected);
        $this->assertEquals("386f0425-3ee8-4688-b73f-272328fe4c71", $this->image->id);
        $this->assertEquals("061d01418b94d4743a98ee26d941e87c", $this->image->checksum);
        $this->assertEquals("057aad9fa85b4e29b23e7888000446ef", $this->image->ownerId);
        $this->assertEquals(983040, $this->image->size);
        $this->assertEquals(0, $this->image->minRam);
        $this->assertNull($this->image->virtualSize);
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

        $this->setupMock('GET', $this->path, null, [], 'GET_image');
        $this->setupMock('GET', 'v2/schemas/image', null, [], 'GET_image_schema');

        $headers = ['Content-Type' => 'application/openstack-images-v2.1-json-patch'];
        $this->setupMock('PATCH', $this->path, $expectedJson, $headers, 'POST_image');

        $this->image->update($opts);
    }

    /**
     * @expectedException \Exception
     */
    public function test_it_throws_exception_if_user_input_does_not_match_schema()
    {
        $this->client->getConfig('base_uri')->shouldBeCalled()->willReturn(new Uri);

        $this->setupMock('GET', $this->path, null, [], 'GET_image');
        $this->setupMock('GET', 'v2/schemas/image', null, [], 'GET_image_schema');

        $this->image->update([
            'minDisk' => 'foo',
        ]);
    }

    public function test_it_deletes()
    {
        $this->setupMock('DELETE', $this->path, null, [], new Response(204));

        $this->image->delete();
    }

    public function test_it_reactivates()
    {
        $this->setupMock('POST', $this->path . '/actions/reactivate', null, [], new Response(204));

        $this->image->reactivate();
    }

    public function test_it_deactivates()
    {
        $this->setupMock('POST', $this->path . '/actions/deactivate', null, [], new Response(204));

        $this->image->deactivate();
    }

    public function test_it_uploads_data_stream()
    {
        $stream  = \GuzzleHttp\Psr7\stream_for('data');
        $headers = ['Content-Type' => 'application/octet-stream'];

        $this->setupMock('PUT', $this->path . '/file', $stream, $headers, new Response(204));

        $this->image->uploadData($stream);
    }

    public function test_it_downloads_data()
    {
        $stream  = \GuzzleHttp\Psr7\stream_for('data');
        $headers = ['Content-Type' => 'application/octet-stream'];
        $response = new Response(200, $headers, $stream);

        $this->setupMock('GET', $this->path . '/file', null, [], $response);

        $this->assertInstanceOf(Stream::class, $this->image->downloadData());
    }

    public function test_it_creates_member()
    {
        $memberId = '8989447062e04a818baf9e073fd04fa7';
        $expectedJson = ['member' => $memberId];

        $this->setupMock('POST', $this->path . '/members', $expectedJson, [], 'GET_member');

        $member = $this->image->addMember('8989447062e04a818baf9e073fd04fa7');
        $this->assertInstanceOf(Member::class, $member);
    }

    public function test_it_lists_members()
    {
        $this->client
            ->request('GET', $this->path . '/members', ['headers' => []])
            ->shouldBeCalled()
            ->willReturn($this->getFixture('GET_members'));

        $count = 0;

        foreach ($this->image->listMembers() as $member) {
            ++$count;
            $this->assertInstanceOf(Member::class, $member);
        }

        $this->assertEquals(2, $count);
    }

    public function test_it_gets_members()
    {
        $this->assertInstanceOf(Member::class, $this->image->getMember('id'));
    }
}
