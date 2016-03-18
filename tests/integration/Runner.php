<?php

namespace OpenCloud\Integration;

class Runner
{
    private $basePath;
    private $logger;
    private $services = [];
    private $namespace;

    public function __construct($basePath, $testNamespace)
    {
        $this->basePath = $basePath;
        $this->namespace = $testNamespace;

        $this->logger = new DefaultLogger();
        $this->assembleServicesFromSamples();
    }

    private function traverse($path)
    {
        return new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS);
    }

    private function assembleServicesFromSamples()
    {
        foreach ($this->traverse($this->basePath) as $servicePath) {
            if ($servicePath->isDir()) {
                foreach ($this->traverse($servicePath) as $versionPath) {
                    $this->services[$servicePath->getBasename()][] = $versionPath->getBasename();
                }
            }
        }
    }

    private function getOpts()
    {
        $opts = getopt('s:v:t:', ['service:', 'version:', 'test::', 'debug::', 'help::']);

        $getOpt = function (array $keys, $default) use ($opts) {
            foreach ($keys as $key) {
                if (isset($opts[$key])) {
                    return $opts[$key];
                }
            }
            return $default;
        };

        return [
            $getOpt(['s', 'service'], 'all'),
            $getOpt(['n', 'version'], 'all'),
            $getOpt(['t', 'test'], ''),
            isset($opts['debug']) ? (int) $opts['debug'] : 0,
        ];
    }

    private function getRunnableServices($service, $version)
    {
        $services = $this->services;

        if ($service != 'all') {
            if (!isset($this->services[$service])) {
                throw new \InvalidArgumentException(sprintf("%s service does not exist", $service));
            }

            $versions = ($version == 'all') ? $this->services[$service] : [$version];
            $services = [$service => $versions];
        }

        return $services;
    }

    /**
     * @return TestInterface
     */
    private function getTest($serviceName, $version, $verbosity)
    {
        $className = sprintf("%s\\%s\\%sTest", $this->namespace, Utils::toCamelCase($serviceName), ucfirst($version));

        if (!class_exists($className)) {
            throw new \RuntimeException(sprintf("%s does not exist", $className));
        }

        $basePath = $this->basePath . DIRECTORY_SEPARATOR . $serviceName . DIRECTORY_SEPARATOR . $version;
        $smClass = sprintf("%s\\SampleManager", $this->namespace);
        $class = new $className($this->logger, new $smClass($basePath, $verbosity));

        if (!($class instanceof TestInterface)) {
            throw new \RuntimeException(sprintf("%s does not implement TestInterface", $className));
        }

        return $class;
    }

    public function runServices()
    {
        list($serviceOpt, $versionOpt, $testMethodOpt, $verbosityOpt) = $this->getOpts();

        foreach ($this->getRunnableServices($serviceOpt, $versionOpt) as $serviceName => $versions) {
            foreach ($versions as $version) {
                $testRunner = $this->getTest($serviceName, $version, $verbosityOpt);

                if ($testMethodOpt) {
                    $testRunner->runOneTest($testMethodOpt);
                } else {
                    $testRunner->runTests();
                }

                $testRunner->teardown();
            }
        }
    }
}
