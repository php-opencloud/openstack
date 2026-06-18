<?php

declare(strict_types=1);

namespace OpenStack\ObjectStore\v1;

use OpenStack\Common\Error\BadResponseError;
use OpenStack\Common\Service\AbstractService;
use OpenStack\ObjectStore\v1\Models\Account;
use OpenStack\ObjectStore\v1\Models\Container;

/**
 * @property Api $api
 */
class Service extends AbstractService
{
    /**
     * Retrieves an Account object.
     */
    public function getAccount(): Account
    {
        return $this->model(Account::class);
    }

    /**
     * Retrieves a collection of container resources in a generator format.
     *
     * @param array         $options {@see \OpenStack\ObjectStore\v1\Api::getAccount}
     * @param callable|null $mapFn   allows a function to be mapped over each element in the collection
     *
     * @return \Generator<mixed, \OpenStack\ObjectStore\v1\Models\Container>
     */
    public function listContainers(array $options = [], ?callable $mapFn = null): \Generator
    {
        $options = array_merge($options, ['format' => 'json']);

        return $this->model(Container::class)->enumerate($this->api->getAccount(), $options, $mapFn);
    }

    /**
     * Retrieves a Container object and populates its name according to the value provided. Please note that the
     * remote API is not contacted.
     *
     * @param string|null $name The unique name of the container
     */
    public function getContainer(?string $name = null): Container
    {
        return $this->model(Container::class, ['name' => $name]);
    }

    /**
     * Creates a new container according to the values provided.
     *
     * @param array $data {@see \OpenStack\ObjectStore\v1\Api::putContainer}
     */
    public function createContainer(array $data): Container
    {
        return $this->getContainer()->create($data);
    }

    /**
     * Checks the existence of a container.
     *
     * @param string $name The name of the container
     *
     * @return bool TRUE if exists, FALSE if it doesn't
     *
     * @throws BadResponseError Thrown for any non 404 status error
     */
    public function containerExists(string $name): bool
    {
        try {
            $this->execute($this->api->headContainer(), ['name' => $name]);

            return true;
        } catch (BadResponseError $e) {
            if (404 === $e->getResponse()->getStatusCode()) {
                return false;
            }
            throw $e;
        }
    }

    /**
     * Creates a temporary URL to access object in private containers.
     * This method loosely follows swift command's way to generate temporary url: `swift tempurl $METHOD $EXPIRE $PATH $KEY`.
     *
     * @param string $method  An HTTP method to allow for this temporary URL. Any of GET, POST, HEAD, PUT, POST, DELETE.
     * @param int    $expires Unix timestamp
     * @param string $path    The full path or storage URL to the Swift object. Example: '/v1/AUTH_account/c/o'  or: 'http://saio:8080/v1/AUTH_account/c/o'
     *                        For prefix based signature, set path to 'prefix:/v1/AUTH_account/container/pre'
     * @param string $key     The secret temporary URL key set on the Swift cluster*
     * @param string $ipRange [OPTIONAL] If present, the temporary URL will be restricted to the given ip or ip range
     * @param string $digest  [OPTIONAL] The digest algorithm to be used may be configured by the operator. Default to sha1.
     *                        Check the tempurl.allowed_digests  entry in the cluster's capabilities response to see which algorithms are supported by your
     *                        deployment;
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    public function tempUrl(string $method, int $expires, string $path, string $key, string $ipRange = null, string $digest = 'sha1'): string
    {
        if (!function_exists('hash_hmac')) {
            throw new \RuntimeException(sprintf('tempUrl requires hash extension enabled.'));
        }

        if ($ipRange) {
            $message = sprintf("ip=%s\n%s\n%s\n%s", $ipRange, $method, $expires, $path);
        } else {
            $message = sprintf("%s\n%s\n%s", $method, $expires, $path);
        }

        $signature = hash_hmac($digest, $message, $key);

        // sha512 requires prefixing signature
        $signature = 'sha512' === $digest ? 'sha512:'.$signature : $signature;

        return sprintf('%s?temp_url_sig=%s&temp_url_expires=%s', $path, $signature, $expires);
    }
}
