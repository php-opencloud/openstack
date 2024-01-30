<?php

namespace OpenStack\Sample;

use RuntimeException;

class SampleManager
{
    protected $basePath;
    protected $paths = [];
    protected $verbosity;

    public function __construct($basePath, $verbosity)
    {
        $this->basePath = $basePath;
        $this->verbosity = $verbosity;
    }

    public function deletePaths(): void
    {
        if (!empty($this->paths)) {
            foreach ($this->paths as $path) {
                unlink($path);
            }
        }
    }

    protected function getGlobalReplacements(): array
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

    public function getConnectionStr(): string
    {
        return str_replace('$openstack =', 'return', $this->getConnectionTemplate());
    }

    protected function getConnectionTemplate(): string
    {
        if ($this->verbosity === 1) {
            $subst = <<<'EOL'
use OpenStack\Sample\DefaultLogger;
use GuzzleHttp\MessageFormatter;

$options = [
    'debugLog'         => true,
    'logger'           => new DefaultLogger(),
    'messageFormatter' => new MessageFormatter(),
];
$openstack = $this->getOpenStack($options);
EOL;
        } elseif ($this->verbosity === 2) {
            $subst = <<<'EOL'
use OpenStack\Sample\DefaultLogger;
use GuzzleHttp\MessageFormatter;

$options = [
    'debugLog'         => true,
    'logger'           => new DefaultLogger(),
    'messageFormatter' => new MessageFormatter(MessageFormatter::DEBUG),
];
$openstack = $this->getOpenStack($options);
EOL;
        } else {
            $subst = <<<'EOL'
use OpenStack\Integration\Utils;

$openstack = $this->getOpenStack();
EOL;
        }

        return $subst;
    }

    public function write(string $path, array $replacements): string
    {
        $replacements = array_merge($this->getGlobalReplacements(), $replacements);

        $sampleFile = rtrim($this->basePath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $path;

        if (!file_exists($sampleFile)) {
            throw new RuntimeException(sprintf("%s does not exist", $sampleFile));
        }

        if(!is_readable($sampleFile)) {
            throw new RuntimeException(sprintf("%s is not readable", $sampleFile));
        }

        $content = strtr(file_get_contents($sampleFile), $replacements);
        $content = str_replace("'vendor/'", "'" . dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . "vendor'", $content);

        $subst = $this->getConnectionTemplate();
        $content = preg_replace('/\([^)]+\)/', '', $content, 1);
        $content = str_replace('$openstack = new OpenStack\OpenStack;', $subst, $content);

        $tmp = tempnam(sys_get_temp_dir(), 'openstack');
        file_put_contents($tmp, $content);

        $this->paths[] = $tmp;

        return $tmp;
    }
}
