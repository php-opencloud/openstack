<?php

namespace OpenCloud\integration;

interface SampleManagerInterface
{
    public function write($path, array $replacements);

    public function deletePaths();
}
