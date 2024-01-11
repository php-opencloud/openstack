<?php

namespace OpenStack\Sample;

use OpenStack\OpenStack;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    private $namePrefix = 'phptest_';

    /** @var \OpenStack\Sample\SampleManager  */
    protected $sampleManager;

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