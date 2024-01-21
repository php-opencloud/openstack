<?php

namespace OpenStack\Sample\Networking\v2;



use OpenStack\Networking\v2\Service;

abstract class TestCase extends \OpenStack\Sample\TestCase
{
    protected function getService(): Service
    {
        return $this->getCachedService(Service::class);
    }

    protected function sampleFile(string $path, array $replacements = []): string
    {
        return parent::sampleFile("Networking/v2/$path", $replacements);
    }
}