<?php

namespace OpenStack\Sample\Images\v2;

use OpenStack\Common\Error\BadResponseError;
use OpenStack\Images\v2\Models\Image;
use OpenStack\Images\v2\Models\Member;

class MemberTest extends TestCase
{
    public function testCreate(): Member
    {
        $image = $this->getService()->createImage([
            'name'            => $this->randomStr(),
            'containerFormat' => 'bare',
            'diskFormat'      => 'qcow2',
            'visibility'      => 'shared',
        ]);

        /** @var Member $member */
        require_once $this->sampleFile('members/create.php', ['{imageId}' => $image->id,]);
        $this->assertInstanceOf(Member::class, $member);

        return $member;
    }

    /**
     * @depends testCreate
     */
    public function testUpdateStatus(Member $createdMember)
    {
        $this->assertEquals(Member::STATUS_PENDING, $createdMember->status);

        require_once $this->sampleFile('members/update_status.php', [
            '{imageId}' => $createdMember->imageId,
            '{memberId}' => $createdMember->id,
        ]);

        $createdMember->retrieve();
        $this->assertEquals(Member::STATUS_ACCEPTED, $createdMember->status);
    }

    /**
     * @depends testCreate
     */
    public function testDelete(Member $createdMember)
    {
        $image = $this->getService()->getImage($createdMember->imageId);

        require_once $this->sampleFile('members/delete.php', [
            '{imageId}' => $createdMember->imageId,
            '{memberId}' => $createdMember->id,
        ]);

        $found = false;
        foreach ($image->listMembers() as $member) {
            if ($member->id === $createdMember->id) {
                $found = true;
            }
        }

        $this->assertFalse($found);
        $image->delete();

        $this->expectException(BadResponseError::class);
        $createdMember->retrieve();
    }
}