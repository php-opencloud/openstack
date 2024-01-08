<?php

namespace OpenStack\Integration\Identity\v3;

use OpenStack\Identity\v3\Models;
use OpenStack\Integration\TestCase;
use OpenStack\Integration\Utils;

class ApplicationCredentialsTest extends TestCase
{
    private $service;

    /**
     * @return \OpenStack\Identity\v3\Service
     */
    private function getService()
    {
        if (null === $this->service) {
            $this->service = Utils::getOpenStack()->identityV3();
        }

        return $this->service;
    }

    public function runTests()
    {
        $this->startTimer();

        $this->logger->info('-> Generate token');
        $this->token();

        $this->outputTimeTaken();
    }

    public function token()
    {
        $this->logStep('Create application credential');

        $name = $this->randomStr();
        $description = $this->randomStr();

        /** @var $applicationCredential \OpenStack\Identity\v3\Models\ApplicationCredential */
        require_once $this->sampleFile(
            'application_credentials/add_application_credential.php',
            [
                '{name}'        => $name,
                '{description}' => $description,
            ]
        );
        self::assertInstanceOf(Models\ApplicationCredential::class, $applicationCredential);
        self::assertEquals($name, $applicationCredential->name);
        self::assertEquals($description, $applicationCredential->description);

        $this->logStep('Create token with id');

        /** @var $token \OpenStack\Identity\v3\Models\Token */
        require_once $this->sampleFile(
            'tokens/generate_token_with_application_credential_id.php',
            [
                '{applicationCredentialId}' => $applicationCredential->id,
                '{secret}'                  => $applicationCredential->secret
            ]
        );
        self::assertInstanceOf(Models\Token::class, $token);

        /** @var $result bool */
        require_once $this->sampleFile('tokens/validate_token.php', ['{tokenId}' => $token->id]);
        self::assertTrue($result);


        $this->logStep('Retrieve application credential');
        $applicationCredentialId = $applicationCredential->id;
        $applicationCredential = null;

        /** @var $applicationCredential \OpenStack\Identity\v3\Models\ApplicationCredential */
        require_once $this->sampleFile(
            'application_credentials/show_application_credential.php',
            ['{applicationCredentialId}' => $applicationCredentialId]
        );
        self::assertInstanceOf(Models\ApplicationCredential::class, $applicationCredential);
        self::assertEquals($name, $applicationCredential->name);
        self::assertEquals($description, $applicationCredential->description);


        $this->logStep('Delete application credential');
        require_once $this->sampleFile(
            'application_credentials/delete_application_credential.php',
            [
                '{applicationCredentialId}' => $applicationCredential->id,
            ]
        );

        /** @var $result bool */
        require_once $this->sampleFile('tokens/validate_token.php', ['{tokenId}' => $token->id]);
        self::assertFalse($result);
    }
}