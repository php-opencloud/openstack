<?php

namespace OpenStack\Sample\Identity\v3;

use OpenStack\Identity\v3\Service;

abstract class TestCase extends \OpenStack\Sample\TestCase
{
    private $service;

    protected function getService(): Service
    {
        if (null === $this->service) {
            $this->service = $this->getOpenStack()->identityV3();
        }

        return $this->service;
    }

    protected function sampleFile(string $path, array $replacements = []): string
    {
        return parent::sampleFile("Identity/v3/$path", $replacements);
    }
}