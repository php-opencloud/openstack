<?php

namespace OpenStack\integration;

interface SampleManagerInterface
{
    public function write($path, array $replacements);

    public function deletePaths();
}
