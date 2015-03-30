<?php

namespace integration\OpenStack\Compute;

use Psr\Log\LoggerInterface;

class v2
{
    private $logger;
    private $basePath;

    public function __construct(LoggerInterface $logger)
    {
        $this->basePath = dirname(__DIR__) . '/../samples/compute/v2/';
        $this->logger = $this->logger;
    }

    public function runTests()
    {
        $this->createServer();
    }

    private function getGlobalReplacements()
    {
        return [
            '{username}' => getenv('OS_USERNAME'),
            '{password}' => getenv('OS_PASSWORD'),
            '{authUrl}'  => getenv('OS_AUTH_URL'),
            '{tenantId}' => getenv('OS_TENANT_ID'),
            '{region}'   => getenv('OS_REGION'),
        ];
    }

    private function randomStr($length = 5)
    {
        $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charsLen = strlen($chars);

        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $chars[rand(0, $charsLen - 1)];
        }

        return 'phptest_' . $randomString;
    }

    private function executeSample(array $replacements, $sampleFilename)
    {
        $replacements = array_merge($this->getGlobalReplacements(), $replacements);

        $sampleFile = rtrim($this->basePath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $sampleFilename;

        if (!file_exists($sampleFile) || !is_readable($sampleFile)) {
            $this->logger->emergency('{file} either does not exist or is not readable', ['file' => $sampleFile]);
            return;
        }

        $content = strtr(file_get_contents($sampleFile), $replacements);

        $content = str_replace("'vendor/'", "'" . dirname(__DIR__) . "/../vendor'", $content);

        $tmp = tempnam(sys_get_temp_dir(), 'openstack');
        file_put_contents($tmp, $content);

        require_once $tmp;

        unlink($tmp);
    }

    private function assertIs($expectedClass, $obj)
    {
        if (!is_object($obj) || !$obj instanceof $expectedClass) {
            $this->logger->emergency('{thing} is not an instance of {class}', [
                'thing' => print_r($obj, true),
                'class' => $expectedClass,
            ]);
        }
    }

    private function assertEquals($expected, $actual)
    {
        if ($expected !== $actual) {
            $this->logger->emergency('{expected} (expected value) is not equal to {actual} (actual value)', [
                'expected' => print_r($expected, true),
                'actual'   => print_r($actual, true),
            ]);
        }
    }

    private function createServer()
    {
        $name = $this->randomStr();
        $imageId = 'e37365c2-5c45-4b73-b4ae-828436d5c569';
        $flavorId = 1;

        $replacements = [
            '{name}'     => $name,
            '{imageId}'  => $imageId,
            '{flavorId}' => $flavorId,
        ];

        $this->executeSample($replacements, 'create_server.php');

        $this->assertIs('OpenStack\Compute\v2\Models\Server', $server);
        $this->assertEquals($name, $server->name);
        $this->assertEquals($imageId, $server->image->id);
        $this->assertEquals($flavorId, $server->flavor->id);
    }
}