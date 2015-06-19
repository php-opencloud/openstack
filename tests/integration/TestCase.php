<?php

namespace OpenStack\Integration;

use Psr\Log\LoggerInterface;

abstract class TestCase extends \PHPUnit_Framework_TestCase
{
    private $logger;
    private $basePath;
    private $paths = [];
    private $startPoint;
    private $lastPoint;

    public function __construct(LoggerInterface $logger)
    {
        $this->basePath = $this->getBasePath();
        $this->logger = $logger;
    }

    abstract protected function getBasePath();

    protected function startTimer()
    {
        $this->startPoint = $this->lastPoint = microtime(true);
    }

    public function deletePaths()
    {
        if (!empty($this->paths)) {
            foreach ($this->paths as $path) {
                unlink($path);
            }
        }
    }

    private function wrapColor($message, $colorPrefix)
    {
        return sprintf("%s%s", $colorPrefix, $message) . "\033[0m\033[1;0m";
    }

    protected function logStep($message, array $context = [])
    {
        $duration = microtime(true) - $this->lastPoint;

        $stepTimeTaken = sprintf('(%s)', $this->formatSecDifference($duration, false));

        if ($duration >= 10) {
            $color = "\033[0m\033[1;31m"; // red
        } elseif ($duration >= 2) {
            $color = "\033[0m\033[1;33m"; // yellow
        } else {
            $color = "\033[0m\033[1;32m"; // green
        }

        $message = '{timeTaken} ' . $message;
        $context['{timeTaken}'] = $this->wrapColor($stepTimeTaken, $color);

        $this->logger->info($message, $context);

        $this->lastPoint = microtime(true);
    }

    protected function getGlobalReplacements()
    {
        return [
            '{userId}' => getenv('OS_USER_ID'),
            '{username}' => getenv('OS_USERNAME'),
            '{password}' => getenv('OS_PASSWORD'),
            '{domainId}' => getenv('OS_DOMAIN_ID'),
            '{authUrl}'  => getenv('OS_AUTH_URL'),
            '{tenantId}' => getenv('OS_TENANT_ID'),
            '{region}'   => getenv('OS_REGION'),
            '{projectId}' => getenv('OS_PROJECT_ID'),
            '{projectName}' => getenv('OS_PROJECT_NAME'),
        ];
    }

    protected function randomStr($length = 5)
    {
        $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charsLen = strlen($chars);

        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $chars[rand(0, $charsLen - 1)];
        }

        return 'phptest_' . $randomString;
    }

    protected function sampleFile(array $replacements, $sampleFilename)
    {
        $replacements = array_merge($this->getGlobalReplacements(), $replacements);

        $sampleFile = rtrim($this->basePath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $sampleFilename;

        if (!file_exists($sampleFile) || !is_readable($sampleFile)) {
            $this->logger->emergency('{file} either does not exist or is not readable', ['{file}' => $sampleFile]);
            return;
        }

        $content = strtr(file_get_contents($sampleFile), $replacements);

        $content = str_replace("'vendor/'", "'" . dirname(__DIR__) . "/../vendor'", $content);

        $tmp = tempnam(sys_get_temp_dir(), 'openstack');
        file_put_contents($tmp, $content);

        $this->paths[] = $tmp;

        return $tmp;
    }

    private function formatMinDifference($duration)
    {
        $output = '';

        if (($minutes = floor($duration / 60)) > 0) {
            $output .= $minutes . 'min' . (($minutes > 1) ? 's' : '');
        }

        if (($seconds = number_format(fmod($duration, 60), 2)) > 0) {
            if ($minutes > 0) {
                $output .= ' ';
            }
            $output .= $seconds . 's';
        }

        return $output;
    }

    private function formatSecDifference($duration)
    {
        return number_format($duration, 2) . 's';
    }

    protected function outputTimeTaken()
    {
        $output = $this->formatMinDifference(microtime(true) - $this->startPoint);

        $this->logger->info('Finished all tests! Time taken: {output}.', ['{output}' => $output]);
    }
}