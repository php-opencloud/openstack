<?php

namespace OpenStack\Sample;

use InvalidArgumentException;
use OpenStack\Common\Service\AbstractService;
use OpenStack\OpenStack;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    private $namePrefix = 'phptest_';

    /** @var \OpenStack\Sample\SampleManager  */
    protected $sampleManager;

    /** @var array<string, \OpenStack\Common\Service\AbstractService> */
    protected $cachedServices = [];

    public function __construct()
    {
        parent::__construct();
        $this->sampleManager = new SampleManager(
            __DIR__ . '/../../samples',
            intval(getenv('SAMPLE_VERBOSITY'))
        );
    }

    protected function getOpenStack(array $options = []): OpenStack
    {
        return new OpenStack($this->getAuthOpts($options));
    }

    /**
     * @param class-string<T> $serviceType
     * @return T
     * @template T of \OpenStack\Common\Service\AbstractService
     *
     * @noinspection PhpFullyQualifiedNameUsageInspection
     */
    protected function getCachedService(string $serviceType): AbstractService
    {
        if (isset($this->cachedServices[$serviceType])) {
            return $this->cachedServices[$serviceType];
        }

        switch ($serviceType) {
            case \OpenStack\BlockStorage\v2\Service::class:
                $service = $this->getOpenStack()->blockStorageV2();
                break;
            case \OpenStack\BlockStorage\v3\Service::class:
                $service = $this->getOpenStack()->blockStorageV3();
                break;
            case \OpenStack\Compute\v2\Service::class:
                $service = $this->getOpenStack()->computeV2();
                break;
            case \OpenStack\Identity\v2\Service::class:
                $service = $this->getOpenStack()->identityV2();
                break;
            case \OpenStack\Identity\v3\Service::class:
                $service = $this->getOpenStack()->identityV3();
                break;
            case \OpenStack\Images\v2\Service::class:
                $service = $this->getOpenStack()->imagesV2();
                break;
            case \OpenStack\Networking\v2\Service::class:
                $service = $this->getOpenStack()->networkingV2();
                break;
            case \OpenStack\ObjectStore\v1\Service::class:
                $service = $this->getOpenStack()->objectStoreV1();
                break;
            default:
                throw new InvalidArgumentException("Unknown service type: $serviceType");
        }

        $this->cachedServices[$serviceType] = $service;

        return $this->cachedServices[$serviceType];
    }

    protected function getAuthOpts(array $options = []): array
    {
        return array_merge(
            [
                'authUrl' => getenv('OS_AUTH_URL'),
                'region'  => getenv('OS_REGION_NAME'),
                'user'    => [
                    'id'       => getenv('OS_USER_ID'),
                    'password' => getenv('OS_PASSWORD'),
                ],
                'scope'   => [
                    'project' => [
                        'id' => getenv('OS_PROJECT_ID'),
                    ],
                ],
            ],
            $options
        );
    }

    /**
     * Creates random string
     */
    protected function randomStr($length = 5): string
    {
        $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charsLen = strlen($chars);

        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $chars[rand(0, $charsLen - 1)];
        }

        return $this->namePrefix . $randomString;
    }

    /**
     * Creates a sample file from a template. It must be included to be executed.
     */
    protected function sampleFile(string $path, array $replacements = []): string
    {
        return $this->sampleManager->write($path, $replacements);
    }
}