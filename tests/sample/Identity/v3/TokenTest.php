<?php

namespace OpenStack\Sample\Identity\v3;

use OpenStack\Identity\v3\Models\Token;

class TokenTest extends TestCase
{
    public function testUsername(): void
    {
        /** @var $token \OpenStack\Identity\v3\Models\Token */
        require_once $this->sampleFile('tokens/generate_token_with_username.php');
        $this->assertInstanceOf(Token::class, $token);
        $this->assertTrue($token->validate());
    }

    public function testUserId(): void
    {
        /** @var $token \OpenStack\Identity\v3\Models\Token */
        require_once $this->sampleFile('tokens/generate_token_with_user_id.php');
        $this->assertInstanceOf(Token::class, $token);
        $this->assertTrue($token->validate());
    }

    public function testScopedToProjectId(): void
    {
        /** @var $token \OpenStack\Identity\v3\Models\Token */
        require_once $this->sampleFile('tokens/generate_token_scoped_to_project_id.php');
        $this->assertInstanceOf(Token::class, $token);
        $this->assertTrue($token->validate());
    }

    public function testScopedToProjectName(): void
    {
        /** @var $token \OpenStack\Identity\v3\Models\Token */
        require_once $this->sampleFile('tokens/generate_token_scoped_to_project_name.php');
        $this->assertInstanceOf(Token::class, $token);
        $this->assertTrue($token->validate());
    }

    public function testFromId(): void
    {
        $tokenId = $this
            ->getService()
            ->generateToken([
                'user' => [
                    'id'       => getenv('OS_USER_ID'),
                    'password' => getenv('OS_PASSWORD'),
                ],
            ])
            ->getId();

        /** @var $token \OpenStack\Identity\v3\Models\Token */
        require_once $this->sampleFile('tokens/generate_token_from_id.php', ['{tokenId}' => $tokenId]);
        $this->assertInstanceOf(Token::class, $token);
        $this->assertTrue($token->validate());

    }

    public function testRevoke(): void
    {
        $token = $this
            ->getService()
            ->generateToken([
                'user' => [
                    'id'       => getenv('OS_USER_ID'),
                    'password' => getenv('OS_PASSWORD'),
                ],
            ]);

        require_once $this->sampleFile('tokens/revoke_token.php', ['{tokenId}' => $token->id]);

        $this->assertFalse($token->validate());
    }

}