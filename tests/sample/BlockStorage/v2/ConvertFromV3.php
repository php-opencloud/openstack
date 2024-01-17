<?php

namespace OpenStack\Sample\BlockStorage\v2;

use OpenStack\BlockStorage\v2\Service;

trait ConvertFromV3
{
    protected function setUp(): void
    {
        parent::setUp();

        if (!getenv('OS_BLOCK_STORAGE_V2')) {
            $this->markTestSkipped('Block Storage v2 API is not available');
        }
    }

    protected function getService(): Service
    {
        return $this->getCachedService(Service::class);
    }

    protected function sampleFile(string $path, array $replacements = []): string
    {
        return parent::sampleFile(
            $path,
            array_merge(
                $replacements,
                ['$openstack->blockStorageV3()' => '$openstack->blockStorageV2()']
            )
        );
    }
}