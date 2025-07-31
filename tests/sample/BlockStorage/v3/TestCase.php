<?php

namespace OpenStack\Sample\BlockStorage\v3;


abstract class TestCase extends \OpenStack\Sample\TestCase
{
    protected function getService(): \OpenStack\BlockStorage\v2\Service
    {
        return $this->getCachedService(\OpenStack\BlockStorage\v3\Service::class);
    }

    protected function sampleFile(string $path, array $replacements = []): string
    {
        return parent::sampleFile("BlockStorage/v3/$path", $replacements);
    }
}