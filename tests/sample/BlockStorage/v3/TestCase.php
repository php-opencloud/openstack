<?php

namespace OpenStack\Sample\BlockStorage\v3;


use OpenStack\BlockStorage\v2\Service;

abstract class TestCase extends \OpenStack\Sample\TestCase
{
    protected $service;

    protected function getService(): Service
    {
        if (null === $this->service) {
            $this->service = $this->getOpenStack()->blockStorageV3();
        }

        return $this->service;
    }

    protected function sampleFile(string $path, array $replacements = []): string
    {
        return parent::sampleFile("BlockStorage/v3/$path", $replacements);
    }
}