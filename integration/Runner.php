<?php

namespace integration\OpenStack;

class Runner
{
    private $logger;

    private $services = [];

    public function __construct()
    {
        $this->logger = new DefaultLogger();

        $this->assembleServicesFromSamples();
        $this->runServices();
    }

    private function traverse($path)
    {
        return new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS);
    }

    private function assembleServicesFromSamples()
    {
        $path = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'samples';

        foreach ($this->traverse($path) as $servicePath) {
            if ($servicePath->isDir()) {
                foreach ($this->traverse($servicePath) as $versionPath) {
                    $this->services[$servicePath->getBasename()][] = $versionPath->getBasename();
                }
            }
        }
    }

    private function getOpts()
    {
        $opts = getopt('s:v:t:', [
            'service:',
            'version:',
            'test::',
            'debug::',
            'help::',
        ]);

        return [
            isset($opts['service']) ? $opts['service'] : 'all',
            isset($opts['version']) ? $opts['version'] : 'all',
            isset($opts['test']) ? $opts['test'] : '',
        ];
    }

    private function getRunnableServices($service, $version)
    {
        $services = $this->services;

        if ($service != 'all') {
            if (!isset($this->services[strtolower($service)])) {
                $this->logger->emergency('{service} does not exist', ['service' => $service]);
            }

            if ($version == 'all') {
                $versions = $this->services[strtolower($service)];
            } else {
                $versions = [$version];
            }

            $services = [$service => $versions];
        }

        return $services;
    }

    private function runServices()
    {
        list ($serviceOpt, $versionOpt, $testMethodOpt) = $this->getOpts();

        $services = $this->getRunnableServices($serviceOpt, $versionOpt, $testMethodOpt);

        foreach ($services as $serviceName => $versions) {
            foreach ($versions as $version) {
                $class = sprintf("%s\\%s\\%s", __NAMESPACE__, ucfirst($serviceName), $version);
                $testRunner = new $class($this->logger);
                if ($testMethodOpt && method_exists($testRunner, $testMethodOpt)) {
                    $testRunner->$testMethodOpt();
                } else {
                    $testRunner->runTests();
                }
            }
        }
    }
}

require_once dirname(__DIR__) . '/vendor/autoload.php';

$runner = new Runner();
$runner->run();