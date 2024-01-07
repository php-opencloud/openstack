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
            [
                '{name}' => $name,
                '{description}' => $description,
            ],
            'application_credentials/add_application_credential.php'
        );
        self::assertInstanceOf(Models\ApplicationCredential::class, $applicationCredential);
        self::assertEquals($name, $applicationCredential->name);
        self::assertEquals($description, $applicationCredential->description);

        $this->logStep('Create token with id');

        /** @var $token \OpenStack\Identity\v3\Models\Token */
        require_once $this->sampleFile(
            [
                '{applicationCredentialId}' => $applicationCredential->id,
                '{secret}' => $applicationCredential->secret
            ],
            'tokens/generate_token_with_application_credential_id.php'
        );
        self::assertInstanceOf(Models\Token::class, $token);

        /** @var $result bool */
        require_once $this->sampleFile(['{tokenId}' => $token->id], 'tokens/validate_token.php');
        self::assertTrue($result);


        $this->logStep('Retrieve application credential');
        $applicationCredentialId = $applicationCredential->id;
        $applicationCredential = null;

        /** @var $applicationCredential \OpenStack\Identity\v3\Models\ApplicationCredential */
        require_once $this->sampleFile(
            ['{applicationCredentialId}' => $applicationCredentialId],
            'application_credentials/show_application_credential.php'
        );
        self::assertInstanceOf(Models\ApplicationCredential::class, $applicationCredential);
        self::assertEquals($name, $applicationCredential->name);
        self::assertEquals($description, $applicationCredential->description);


        $this->logStep('Delete application credential');
        require_once $this->sampleFile(
            [
                '{applicationCredentialId}' => $applicationCredential->id,
            ],
            'application_credentials/delete_application_credential.php'
        );

        /** @var $result bool */
        require_once $this->sampleFile(['{tokenId}' => $token->id], 'tokens/validate_token.php');
        self::assertFalse($result);
    }
}