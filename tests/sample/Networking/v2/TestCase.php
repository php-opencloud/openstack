<?php

namespace OpenStack\Sample\Networking\v2;



use OpenStack\Networking\v2\Service;

abstract class TestCase extends \OpenStack\Sample\TestCase
{
    private $service;
    private $serviceLayer3;

    protected function getService(): Service
    {
        if (null === $this->service) {
            $this->service = $this->getOpenStack()->networkingV2();
        }

        return $this->service;
    }

    protected function getServiceLayer3(): \OpenStack\Networking\v2\Extensions\Layer3\Service
    {
        if (null === $this->serviceLayer3) {
            $this->serviceLayer3 = $this->getOpenStack()->networkingV2ExtLayer3();
        }

        return $this->serviceLayer3;
    }

    protected function sampleFile(string $path, array $replacements = []): string
    {
        return parent::sampleFile("Networking/v2/$path", $replacements);
    }
}