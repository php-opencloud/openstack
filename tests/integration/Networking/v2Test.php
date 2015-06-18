<?php

namespace OpenStack\Integration\Networking;

use OpenStack\Networking\v2\Models\Network;
use OpenStack\Integration\TestCase;
use OpenStack\OpenStack;

class V2Test extends TestCase
{
    private $service;
    private $networkId;

    private function getService()
    {
        if (null === $this->service) {
            $this->service = (new OpenStack())->networkingV2(['region' => getenv('OS_REGION')]);
        }

        return $this->service;
    }

    protected function getBasePath()
    {
        return __DIR__ . '/../../../samples/networking/v2/';
    }

    public function runTests()
    {
        $this->startTimer();

        $this->createNetwork();

        try {
            $this->retrieveNetwork();
        } finally {
            // Teardown
            $this->deleteNetwork();
        }

        $this->outputTimeTaken();
    }

    private function createNetwork()
    {
        $this->networkId = 'f5cc56db-db25-4488-8371-c507951b2631';

        $this->logStep('Created network {id}', ['{id}' => $this->networkId]);
    }

    private function retrieveNetwork()
    {
        $replacements = ['{networkId}' => $this->networkId];

        /** @var $network \OpenStack\Networking\v2\Models\Network */
        $path = $this->sampleFile($replacements, 'get_network.php');
        require_once $path;

        $this->assertInstanceOf('OpenStack\Networking\v2\Models\Network', $network);
        $this->assertEquals($this->networkId, $network->id);

        $this->logStep('Retrieved the details of network ID', ['ID' => $this->networkId]);
    }

    private function deleteNetwork()
    {
        $this->logStep('Deleted network ID', ['ID' => $this->networkId]);
    }
}
