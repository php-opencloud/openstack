<?php

namespace OpenStack\Integration;

use GuzzleHttp\Client;
use OpenStack\Identity\v2\Api;
use OpenStack\Identity\v2\Service;
use OpenStack\Common\Transport\HandlerStack;
use Psr\Log\LoggerInterface;

abstract class TestCase extends \PHPUnit_Framework_TestCase
{
    protected $logger;
    private $basePath;
    private $paths = [];
    private $startPoint;
    private $lastPoint;
    private $debug;
    protected $defaultLogging;

    public function __construct(LoggerInterface $logger, $debug = false)
    {
        $this->basePath = $this->getBasePath();
        $this->logger = $logger;
        $this->debug = $debug;
    }

    abstract protected function getBasePath();

    protected function getAuthOptsV3()
    {
        return [
            'authUrl' => getenv('OS_AUTH_URL'),
            'region'  => getenv('OS_REGION'),
            'user'    => [
                'id'       => getenv('OS_USER_ID'),
                'password' => getenv('OS_PASSWORD'),
            ],
            'scope'   => [
                'project' => [
                    'id' => getenv('OS_PROJECT_ID'),
                ]
            ]
        ];
    }

    protected function getAuthOptsV2()
    {
        $httpClient = new Client([
            'base_uri' => getenv('OS_AUTH_URL'),
            'handler'  => HandlerStack::create(),
        ]);
        $identityService = new Service($httpClient, new Api);
        return [
            'authUrl'         => getenv('OS_AUTH_URL'),
            'region'          => getenv('OS_REGION_NAME'),
            'username'        => getenv('OS_USERNAME'),
            'password'        => getenv('OS_PASSWORD'),
            'tenantName'      => getenv('OS_TENANT_NAME'),
            'identityService' => $identityService,
        ];
    }

    protected function getAuthOpts()
    {
        return getenv('OS_IDENTITY_API_VERSION') == '2.0' ?
            $this->getAuthOptsV2() : $this->getAuthOptsV3();
    }

    public function startTimer()
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
            '{userId}'      => getenv('OS_USER_ID'),
            '{username}'    => getenv('OS_USERNAME'),
            '{password}'    => getenv('OS_PASSWORD'),
            '{domainId}'    => getenv('OS_DOMAIN_ID'),
            '{authUrl}'     => getenv('OS_AUTH_URL'),
            '{tenantId}'    => getenv('OS_TENANT_ID'),
            '{region}'      => getenv('OS_REGION'),
            '{projectId}'   => getenv('OS_PROJECT_ID'),
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

    protected function getConnectionTemplate($debug)
    {
        if ($debug) {
            $subst = <<<'EOL'
use OpenStack\Integration\DefaultLogger;
use OpenStack\Integration\Utils;
use GuzzleHttp\MessageFormatter;

$options = [
    'debugLog'         => true,
    'logger'           => new DefaultLogger(),
    'messageFormatter' => new MessageFormatter(),
];
$openstack = new OpenStack\OpenStack(Utils::getAuthOpts($options));
EOL;
        } else {
            $subst = <<<'EOL'
use OpenStack\Integration\Utils;

$openstack = new OpenStack\OpenStack(Utils::getAuthOpts());
EOL;
        }
        return $subst;
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

        $subst = $this->getConnectionTemplate($this->debug);
        $content = preg_replace('/\([^)]+\)/', '', $content, 1);
        $content = str_replace('$openstack = new OpenStack\OpenStack;', $subst, $content);

        $tmp = tempnam(sys_get_temp_dir(), 'openstack');
        file_put_contents($tmp, $content);

        $this->paths[] = $tmp;

        if ($this->defaultLogging === true) {
            $msg = ucfirst(str_replace('_', ' ', basename($sampleFile, '.php')));
            $this->logStep($msg);
        }

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