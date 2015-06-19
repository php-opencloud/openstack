<?php

namespace OpenStack\Integration\Identity;

use OpenStack\Identity\v3\Models\Token;
use OpenStack\Integration\TestCase;

class V3Test extends TestCase
{
    private function getService()
    {
        if (null === $this->service) {
            $this->service = (new OpenStack())->identityV3(['region' => getenv('OS_REGION')]);
        }

        return $this->service;
    }

    protected function getBasePath()
    {
        return __DIR__ . '/../../../samples/identity/v3/';
    }

    public function runTests()
    {
        $this->runTokenTests();

//        $this->runCredentialTests();
//        $this->runDomainTests();
//        $this->runEndpointTests();
//        $this->runGroupTests();
//        $this->runPolicyTests();
//        $this->runProjectTests();
//        $this->runRoleTests();
//        $this->runServiceTests();
//        $this->runUserTests();
    }

    private function runTokenTests()
    {
        $replacements = [
            '{username}'  => getenv('OS_USERNAME'),
            '{password}'  => getenv('OS_PASSWORD'),
            '{domainId}'  => getenv('OS_DOMAIN_ID'),
            '{projectId}' => getenv('OS_PROJECT_ID'),
            '{projectName}' => getenv('OS_PROJECT_NAME'),
        ];

        /** @var $token \OpenStack\Identity\v3\Models\Token */
        $path = $this->sampleFile($replacements, 'tokens/generate_token_with_username.php');
        require_once $path;
        $this->assertInstanceOf(Token::class, $token);

        $replacements['{userId}'] = $token->user->id;

        /** @var $token \OpenStack\Identity\v3\Models\Token */
        $path = $this->sampleFile($replacements, 'tokens/generate_token_with_user_id.php');
        require_once $path;
        $this->assertInstanceOf(Token::class, $token);

        $replacements['{tokenId}'] = $token->id;

        /** @var $token \OpenStack\Identity\v3\Models\Token */
        $path = $this->sampleFile($replacements, 'tokens/generate_token_scoped_to_project_id.php');
        require_once $path;
        $this->assertInstanceOf(Token::class, $token);

        /** @var $token \OpenStack\Identity\v3\Models\Token */
        $path = $this->sampleFile($replacements, 'tokens/generate_token_scoped_to_project_name.php');
        require_once $path;
        $this->assertInstanceOf(Token::class, $token);

        /** @var $token \OpenStack\Identity\v3\Models\Token */
        $path = $this->sampleFile($replacements, 'tokens/generate_token_from_id.php');
        require_once $path;
        $this->assertInstanceOf(Token::class, $token);

        /** @var $result bool */
        $path = $this->sampleFile($replacements, 'tokens/validate_token.php');
        require_once $path;
        $this->assertTrue($result);

        $path = $this->sampleFile($replacements, 'tokens/revoke_token.php');
        require_once $path;

        /** @var $result bool */
        $path = $this->sampleFile($replacements, 'tokens/validate_token.php');
        require_once $path;
        $this->assertFalse($result);
    }

    private function runCredentialTests()
    {
        /** @var $server \OpenStack\Compute\v2\Models\Server */
        $path = $this->sampleFile($replacements, 'add_cred.php');
        require_once $path;
    }
}