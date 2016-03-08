<?php

namespace OpenCloud\integration;

class SampleManager implements SampleManagerInterface
{
    protected $basePath;
    protected $paths = [];
    protected $verbosity;

    public function __construct($basePath, $verbosity)
    {
        $this->basePath = $basePath;
        $this->verbosity = $verbosity;
    }

    public function deletePaths()
    {
        if (!empty($this->paths)) {
            foreach ($this->paths as $path) {
                unlink($path);
            }
        }
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

    protected function getConnectionTemplate()
    {
        if ($this->verbosity === 1) {
            $subst = <<<'EOL'
use OpenCloud\Integration\DefaultLogger;
use OpenCloud\Integration\Utils;
use GuzzleHttp\MessageFormatter;

$options = [
    'debugLog'         => true,
    'logger'           => new DefaultLogger(),
    'messageFormatter' => new MessageFormatter(),
];
$openstack = new OpenCloud\OpenCloud(Utils::getAuthOpts($options));
EOL;
        } elseif ($this->verbosity === 2) {
            $subst = <<<'EOL'
use OpenCloud\Integration\DefaultLogger;
use OpenCloud\Integration\Utils;
use GuzzleHttp\MessageFormatter;

$options = [
    'debugLog'         => true,
    'logger'           => new DefaultLogger(),
    'messageFormatter' => new MessageFormatter(MessageFormatter::DEBUG),
];
$openstack = new OpenCloud\OpenCloud(Utils::getAuthOpts($options));
EOL;
        } else {
            $subst = <<<'EOL'
use OpenCloud\Integration\Utils;

$openstack = new OpenCloud\OpenCloud(Utils::getAuthOpts());
EOL;
        }

        return $subst;
    }

    public function write($path, array $replacements)
    {
        $replacements = array_merge($this->getGlobalReplacements(), $replacements);

        $sampleFile = rtrim($this->basePath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $path;

        if (!file_exists($sampleFile) || !is_readable($sampleFile)) {
            throw new \RuntimeException(sprintf("%s either does not exist or is not readable", $sampleFile));
        }

        $content = strtr(file_get_contents($sampleFile), $replacements);
        $content = str_replace("'vendor/'", "'" . dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . "vendor'", $content);

        $subst = $this->getConnectionTemplate();
        $content = preg_replace('/\([^)]+\)/', '', $content, 1);
        $content = str_replace('$openstack = new OpenCloud\OpenCloud;', $subst, $content);

        $tmp = tempnam(sys_get_temp_dir(), 'openstack');
        file_put_contents($tmp, $content);

        $this->paths[] = $tmp;

        return $tmp;
    }
}
