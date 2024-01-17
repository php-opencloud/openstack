<?php

namespace OpenStack\Sample\Identity\v3;

use OpenStack\Identity\v3\Service;

abstract class TestCase extends \OpenStack\Sample\TestCase
{
    protected function getService(): Service
    {
        return $this->getCachedService(Service::class);
    }

    protected function sampleFile(string $path, array $replacements = []): string
    {
        return parent::sampleFile("Identity/v3/$path", $replacements);
    }
}