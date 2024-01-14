<?php

namespace OpenStack\Sample\Images\v2;


use OpenStack\Images\v2\Service;

abstract class TestCase extends \OpenStack\Sample\TestCase
{
    private $service;

    protected function getService(): Service
    {
        if (null === $this->service) {
            $this->service = $this->getOpenStack()->imagesV2();
        }

        return $this->service;
    }

    protected function sampleFile(string $path, array $replacements = []): string
    {
        return parent::sampleFile("Images/v2/$path", $replacements);
    }
}