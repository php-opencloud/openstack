<?php

namespace OpenStack\Sample\Networking\v2;

use OpenStack\Common\Error\BadResponseError;
use OpenStack\Networking\v2\Models\Network;

class NetworkTest extends TestCase
{
    public function testCreateBatch()
    {
        /** @var Network[] $networks */
        require_once $this->sampleFile('networks/create_batch.php', [
            '{networkName1}' => $this->randomStr(),
            '{networkName2}' => $this->randomStr(),
        ]);

        foreach ($networks as $network) {
            $this->assertInstanceOf(Network::class, $network);
            $this->assertNotEmpty($network->id);

            $this->getService()->getNetwork($network->id)->delete();
        }
    }

    public function testCreate(): Network
    {
        /** @var \OpenStack\Networking\v2\Models\Network $network */
        require_once $this->sampleFile('networks/create.php', ['{networkName}' => $this->randomStr()]);

        $this->assertInstanceOf(Network::class, $network);
        $this->assertNotEmpty($network->id);

        return $network;
    }

    /**
     * @depends testCreate
     */
    public function testUpdate(Network $createdNetwork)
    {
        $newName = $this->randomStr();

        require_once $this->sampleFile('networks/update.php', [
            '{networkId}' => $createdNetwork->id,
            '{newName}' => $newName,
        ]);

        $createdNetwork->retrieve();
        $this->assertEquals($newName, $createdNetwork->name);
    }

    /**
     * @depends testCreate
     */
    public function testRead(Network $createdNetwork)
    {
        /** @var \OpenStack\Networking\v2\Models\Network $network */
        require_once $this->sampleFile('networks/read.php', ['{networkId}' => $createdNetwork->id]);

        $this->assertInstanceOf(Network::class, $network);
        $this->assertEquals($createdNetwork->id, $network->id);
        $this->assertEquals($createdNetwork->name, $network->name);
    }

    /**
     * @depends testCreate
     */
    public function testDelete(Network $createdNetwork)
    {
        require_once $this->sampleFile('networks/delete.php', ['{networkId}' => $createdNetwork->id]);

        foreach ($this->getService()->listNetworks() as $network) {
            if ($network->id == $createdNetwork->id) {
                $this->fail('The network was not deleted');
            }
        }

        $this->expectException(BadResponseError::class);
        $this->getService()->getNetwork($createdNetwork->id)->retrieve();
    }
}