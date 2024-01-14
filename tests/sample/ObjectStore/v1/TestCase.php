<?php

namespace OpenStack\Sample\ObjectStore\v1;


use OpenStack\ObjectStore\v1\Service;

abstract class TestCase extends \OpenStack\Sample\TestCase
{
    private $service;

    protected function getService(): Service
    {
        if (null === $this->service) {
            $this->service = $this->getOpenStack()->objectStoreV1();
        }

        return $this->service;
    }

    protected function sampleFile(string $path, array $replacements = []): string
    {
        return parent::sampleFile("ObjectStore/v1/$path", $replacements);
    }
}