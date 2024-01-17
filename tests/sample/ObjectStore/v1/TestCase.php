<?php

namespace OpenStack\Sample\ObjectStore\v1;


use OpenStack\ObjectStore\v1\Service;

abstract class TestCase extends \OpenStack\Sample\TestCase
{
    protected function getService(): Service
    {
        return $this->getCachedService(Service::class);
    }

    protected function sampleFile(string $path, array $replacements = []): string
    {
        return parent::sampleFile("ObjectStore/v1/$path", $replacements);
    }
}