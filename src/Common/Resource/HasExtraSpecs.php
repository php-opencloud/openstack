<?php declare(strict_types=1);

namespace OpenStack\Common\Resource;

use Psr\Http\Message\ResponseInterface;

interface HasExtraSpecs
{
    /**
     * Retrieves extra specs for the resource in the form of an associative array or hash. Each key represents the
     * extra specs item's name, and each value represents extra specs item's remote value.
     *
     * @return array
     */
    public function getExtraSpecs(): array;

    /**
     * Merges a set of new values with those which already exist (on the remote API) for a resource. For example, if
     * the resource has this extra specs already set:
     *
     *  Foo: val1
     *  Bar: val2
     *
     * and mergeExtraSpecs(['Foo' => 'val3', 'Baz' => 'val4']); is called, then the resource will have the following
     * extra specs:
     *
     *  Foo: val3
     *  Bar: val2
     *  Baz: val4
     *
     * You will notice that any extra specs items which are not specified in the call are preserved.
     *
     * @param array $extra specs The new extra specs items
     *
     * @return void
     */
    public function mergeExtraSpecs(array $extraSpecs);

    /**
     * Deletes an extra spec, by key, for a flavor, by ID.
     *
     * @param string $extra specs key
     *
     * @return void
     */
    public function deleteExtraSpec($extraSpecs);

    /**
     * Extracts extra specs from a response object and returns it in the form of an associative array.
     *
     * @param ResponseInterface $response
     *
     * @return array
     */
    public function parseExtraSpecs(ResponseInterface $response): array;
}
