<?php

namespace OpenStack\Sample\Images\v2;


use OpenStack\Images\v2\Service;

abstract class TestCase extends \OpenStack\Sample\TestCase
{
    protected function getService(): Service
    {
        return $this->getCachedService(Service::class);
    }

    protected function sampleFile(string $path, array $replacements = []): string
    {
        return parent::sampleFile("Images/v2/$path", $replacements);
    }
}