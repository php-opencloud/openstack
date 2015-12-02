<?php

namespace OpenStack\Test\Common\JsonSchema;

use JsonSchema\Validator;
use OpenStack\Common\JsonSchema\Schema;
use OpenStack\Test\TestCase;

class SchemaTest extends TestCase
{
    private $schema;
    private $validator;

    public function setUp()
    {
        $this->validator = $this->prophesize(Validator::class);
        $this->schema = new Schema([], $this->validator->reveal());
    }

    public function test_it_gets_errors()
    {
        $this->validator->getErrors()
            ->shouldBeCalled()
            ->willReturn([]);

        $this->assertEquals([], $this->schema->getErrors());
    }

    public function test_it_gets_error_string()
    {
        $this->validator->getErrors()
            ->shouldBeCalled()
            ->willReturn([['property' => 'foo', 'message' => 'bar']]);

        $errorMsg = sprintf("Provided values do not validate. Errors:\n[foo] bar\n");

        $this->assertEquals($errorMsg, $this->schema->getErrorString());
    }
}