<?php

namespace integration\OpenStack;

class Runner
{
    private $services = [
        'compute' => ['v2'],
    ];

    public function __construct()
    {
        $logger = new DefaultLogger();

        $opts = getopt('s:v:t:', [
            'service:',
            'version:',
            'test::',
            'debug::',
            'help::',
        ]);

        $service = isset($opts['service']) ? $opts['service'] : 'all';
        $version = isset($opts['version']) ? $opts['version'] : 'all';
        $testMethod = isset($opts['test']) ? $opts['test'] : '';

        if ($service != 'all') {
            if (!isset($this->services[strtolower($service)])) {
                $logger->emergency('{service} does not exist', ['service' => $service]);
            }

            if ($version == 'all') {
                $versions = $this->services[strtolower($service)];
            } else {
                $versions = [$version];
            }

            $services = [$service => $versions];
        } else {
            $services = $this->services;
        }

        foreach ($services as $serviceName => $versions) {
            foreach ($versions as $version) {
                $class = __NAMESPACE__ . '\\' . ucfirst($serviceName) . '\\' . $version;

                $testRunner = new $class($logger);
                if ($testMethod && method_exists($testRunner, $testMethod)) {
                    $testRunner->$testMethod();
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