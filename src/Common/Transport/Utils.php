<?php

namespace OpenStack\Common\Transport;

use Psr\Http\Message\ResponseInterface;

class Utils
{
    public static function jsonDecode(ResponseInterface $response)
    {
        $jsonErrors = [
            JSON_ERROR_DEPTH => 'JSON_ERROR_DEPTH - Maximum stack depth exceeded',
            JSON_ERROR_STATE_MISMATCH => 'JSON_ERROR_STATE_MISMATCH - Underflow or the modes mismatch',
            JSON_ERROR_CTRL_CHAR => 'JSON_ERROR_CTRL_CHAR - Unexpected control character found',
            JSON_ERROR_SYNTAX => 'JSON_ERROR_SYNTAX - Syntax error, malformed JSON',
            JSON_ERROR_UTF8 => 'JSON_ERROR_UTF8 - Malformed UTF-8 characters, possibly incorrectly encoded'
        ];

        $data = json_decode((string) $response->getBody(), true);

        if (JSON_ERROR_NONE !== json_last_error()) {
            $last = json_last_error();
            throw new \InvalidArgumentException(
                'Unable to parse JSON data: ' . (isset($jsonErrors[$last]) ? $jsonErrors[$last] : 'Unknown error')
            );
        }

        return $data;
    }

    /**
     * Method for flattening a nested array.
     *
     * @param array $data The nested array
     * @param null  $key  The key to extract
     *
     * @return array
     */
    public static function flattenJson(array $data, $key = null)
    {
        return (!empty($data) && $key && isset($data[$key])) ? $data[$key] : $data;
    }
}