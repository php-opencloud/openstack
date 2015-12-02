<?php

namespace OpenStack\Common\Transport;

use function GuzzleHttp\Psr7\uri_for;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

class Utils
{
    public static function jsonDecode(ResponseInterface $response, $assoc = true)
    {
        $jsonErrors = [
            JSON_ERROR_DEPTH => 'JSON_ERROR_DEPTH - Maximum stack depth exceeded',
            JSON_ERROR_STATE_MISMATCH => 'JSON_ERROR_STATE_MISMATCH - Underflow or the modes mismatch',
            JSON_ERROR_CTRL_CHAR => 'JSON_ERROR_CTRL_CHAR - Unexpected control character found',
            JSON_ERROR_SYNTAX => 'JSON_ERROR_SYNTAX - Syntax error, malformed JSON',
            JSON_ERROR_UTF8 => 'JSON_ERROR_UTF8 - Malformed UTF-8 characters, possibly incorrectly encoded'
        ];

        $data = json_decode((string) $response->getBody(), $assoc);

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
    public static function flattenJson($data, $key = null)
    {
        return (!empty($data) && $key && isset($data[$key])) ? $data[$key] : $data;
    }

    /**
     * Method for normalize an URL string.
     *
     * Append the http:// prefix if not present, and add a
     * closing url separator when missing.
     *
     * @param string $url The url representation.
     *
     * @return string
     */
    public static function normalizeUrl($url)
    {
        if (strpos($url, 'http') === false) {
            $url = 'http://' . $url;
        }

        return rtrim($url, '/') . '/';
    }

    /**
     * Add an unlimited list of paths to a given URI.
     *
     * @param UriInterface $uri
     * @param              ...$paths
     *
     * @return UriInterface
     */
    public static function addPaths(UriInterface $uri, ...$paths)
    {
        return uri_for(rtrim((string) $uri, '/') . '/' . implode('/', $paths));
    }

    public static function appendPath(UriInterface $uri, $path)
    {
        return uri_for(rtrim((string) $uri, '/') . '/' . $path);
    }
}
