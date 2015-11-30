<?php

namespace OpenStack\Test\Common;

use OpenStack\Common\JsonPatch;
use OpenStack\Test\TestCase;

class JsonPatchTest extends TestCase
{
    public function testAll()
    {
        $fixtures = json_decode(file_get_contents(__DIR__ . '/Fixtures/jsonPatchTests.json'));

        foreach ($fixtures as $fixture) {
            if (isset($fixture->disabled) || !isset($fixture->expected)) {
                continue;
            }

            $srcJson = json_encode($fixture->doc);
            $desJson = json_encode($fixture->expected);

            $expected = json_encode($fixture->patch, JSON_UNESCAPED_SLASHES);
            $actual = (new JsonPatch())->makeDiff($srcJson, $desJson);

            $msg  = isset($fixture->comment) ? sprintf("Failed asserting test: %s\n", $fixture->comment) : '';
            $msg .= sprintf("Doc: %s\nExpected: %s", $srcJson, $desJson);

            $this->assertEquals($expected, $actual, $msg);
        }
    }
}