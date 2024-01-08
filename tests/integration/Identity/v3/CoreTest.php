<?php

namespace OpenStack\Integration\Identity\v3;

use OpenStack\Identity\v3\Models;
use OpenStack\Integration\TestCase;
use OpenStack\Integration\Utils;

class CoreTest extends TestCase
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

        $this->logger->info('-> Tokens');
        $this->tokens();

        $this->logger->info('-> Domains');
        $this->domains();

        $this->logger->info('-> Endpoints');
        $this->endpoints();

        $this->logger->info('-> Services');
        $this->services();

        $this->logger->info('-> Groups');
        $this->groups();

        $this->logger->info('-> Projects');
        $this->projects();

        $this->logger->info('-> Roles');
        $this->roles();

        $this->logger->info('-> Users');
        $this->users();

        $this->outputTimeTaken();
    }

    public function tokens()
    {
        $this->logStep('Generate token with user name');

        /** @var $token \OpenStack\Identity\v3\Models\Token */
        require_once $this->sampleFile('tokens/generate_token_with_username.php');
        self::assertInstanceOf(Models\Token::class, $token);

        /** @var $result bool */
        require_once $this->sampleFile('tokens/validate_token.php', ['{tokenId}' => $token->id]);
        self::assertTrue($result);


        $this->logStep('Generate token with user id');

        /** @var $token \OpenStack\Identity\v3\Models\Token */
        require_once $this->sampleFile('tokens/generate_token_with_user_id.php');
        self::assertInstanceOf(Models\Token::class, $token);

        /** @var $result bool */
        require_once $this->sampleFile('tokens/validate_token.php', ['{tokenId}' => $token->id]);
        self::assertTrue($result);


        $this->logStep('Generate token scoped to project id');
        /** @var $token \OpenStack\Identity\v3\Models\Token */
        require_once $this->sampleFile('tokens/generate_token_scoped_to_project_id.php');
        self::assertInstanceOf(Models\Token::class, $token);

        /** @var $result bool */
        require_once $this->sampleFile('tokens/validate_token.php', ['{tokenId}' => $token->id]);
        self::assertTrue($result);


        $this->logStep('Generate token scoped to project name');

        /** @var $token \OpenStack\Identity\v3\Models\Token */
        require_once $this->sampleFile('tokens/generate_token_scoped_to_project_name.php');
        self::assertInstanceOf(Models\Token::class, $token);

        /** @var $result bool */
        require_once $this->sampleFile('tokens/validate_token.php', ['{tokenId}' => $token->id]);
        self::assertTrue($result);


        $this->logStep('Generate token from id');
        /** @var $token \OpenStack\Identity\v3\Models\Token */
        require_once $this->sampleFile('tokens/generate_token_from_id.php', ['{tokenId}' => $token->id]);
        self::assertInstanceOf(Models\Token::class, $token);

        /** @var $result bool */
        require_once $this->sampleFile('tokens/validate_token.php', ['{tokenId}' => $token->id]);
        self::assertTrue($result);


        $this->logStep('Revoke token');

        require_once $this->sampleFile('tokens/revoke_token.php', ['{tokenId}' => $token->id]);

        /** @var $result bool */
        require_once $this->sampleFile('tokens/validate_token.php', ['{tokenId}' => $token->id]);
        self::assertFalse($result);
    }

    public function domains()
    {
        $this->logStep('Create domain');

        $replacements = [
            '{name}'        => $this->randomStr(),
            '{description}' => $this->randomStr(),
        ];

        /** @var $domain \OpenStack\Identity\v3\Models\Domain */
        require_once $this->sampleFile('domains/add_domain.php', $replacements);
        self::assertInstanceOf(Models\Domain::class, $domain);


        $this->logStep('List domains');

        $replacements['{domainId}'] = $domain->id;

        require_once $this->sampleFile('domains/list_domains.php', []);


        $this->logStep('Show domain');

        /** @var $domain \OpenStack\Identity\v3\Models\Domain */
        require_once $this->sampleFile('domains/show_domain.php', $replacements);
        self::assertInstanceOf(Models\Domain::class, $domain);


        $this->logStep('Grant and revoke group role');

        $parentRole = $this->getService()->createRole(['name' => $this->randomStr()]);
        $group = $this->getService()->createGroup(['name' => $this->randomStr(), 'domainId' => $domain->id]);

        require_once $this->sampleFile('domains/grant_group_role.php', $replacements + ['{groupId}' => $group->id, '{roleId}' => $parentRole->id]);

        /** @var $result bool */
        require_once $this->sampleFile('domains/check_group_role.php', $replacements + ['{groupId}' => $group->id, '{roleId}' => $parentRole->id]);
        self::assertTrue($result);

        require_once $this->sampleFile('domains/list_group_roles.php', $replacements + ['{groupId}' => $group->id]);

        require_once $this->sampleFile('domains/revoke_group_role.php', $replacements + ['{groupId}' => $group->id, '{roleId}' => $parentRole->id]);

        $group->delete();


        $this->logStep('Grant and revoke user role');

        $user = $this->getService()->createUser(['name' => $this->randomStr(), 'domainId' => $domain->id]);

        require_once $this->sampleFile('domains/grant_user_role.php', $replacements + ['{domainUserId}' => $user->id, '{roleId}' => $parentRole->id]);

        /** @var $result bool */
        require_once $this->sampleFile('domains/check_user_role.php', $replacements + ['{domainUserId}' => $user->id, '{roleId}' => $parentRole->id]);
        self::assertTrue($result);

        require_once $this->sampleFile('domains/list_user_roles.php', $replacements + ['{domainUserId}' => $user->id]);

        require_once $this->sampleFile('domains/revoke_user_role.php', $replacements + ['{domainUserId}' => $user->id, '{roleId}' => $parentRole->id]);

        $user->delete();
        $parentRole->delete();


        $this->logStep('Update domain');

        /** @var $domain \OpenStack\Identity\v3\Models\Domain */
        require_once $this->sampleFile('domains/update_domain.php', $replacements);
        self::assertInstanceOf(Models\Domain::class, $domain);


        $this->logStep('Delete domain');

        require_once $this->sampleFile('domains/delete_domain.php', $replacements);
    }

    public function endpoints()
    {
        $service = $this->getService()->createService(['name' => $this->randomStr(), 'type' => 'volume', 'description' => $this->randomStr()]);

        $replacements = [
            '{endpointName}' => $this->randomStr(),
            '{serviceId}' => $service->id,
            '{endpointUrl}' => getenv('OS_AUTH_URL'),
            '{region}' => 'RegionOne',
        ];

        /** @var $endpoint \OpenStack\Identity\v3\Models\Endpoint */
        require_once $this->sampleFile('endpoints/add_endpoint.php', $replacements);
        self::assertInstanceOf(Models\Endpoint::class, $endpoint);

        $replacements['{endpointId}'] = $endpoint->id;

        require_once $this->sampleFile('endpoints/list_endpoints.php', $replacements);

        /** @var $endpoint \OpenStack\Identity\v3\Models\Endpoint */
        require_once $this->sampleFile('endpoints/update_endpoint.php', $replacements);

        require_once $this->sampleFile('endpoints/delete_endpoint.php', $replacements);

        $service->delete();
    }

    public function services()
    {
        $replacements = [
            '{serviceName}' => $this->randomStr(),
            '{serviceType}' => $this->randomStr(),
        ];

        /** @var $service \OpenStack\Identity\v3\Models\Service */
        require_once $this->sampleFile('services/add_service.php', $replacements);
        self::assertInstanceOf(Models\Service::class, $service);

        $replacements['{serviceId}'] = $service->id;

        require_once $this->sampleFile('services/list_services.php', $replacements);

        /** @var $service \OpenStack\Identity\v3\Models\Service */
        require_once $this->sampleFile('services/update_service.php', $replacements);
        self::assertInstanceOf(Models\Service::class, $service);

        /** @var $service \OpenStack\Identity\v3\Models\Service */
        require_once $this->sampleFile('services/get_service.php', $replacements);
        self::assertInstanceOf(Models\Service::class, $service);

        require_once $this->sampleFile('services/delete_service.php', $replacements);
    }

    public function groups()
    {
        $groupUser = $this->getService()->createUser(['name' => $this->randomStr()]);

        /** @var $group \OpenStack\Identity\v3\Models\Group */
        require_once $this->sampleFile('groups/add_group.php', ['{name}' => $this->randomStr(), '{description}' => $this->randomStr()]);
        self::assertInstanceOf(Models\Group::class, $group);

        $replacements = ['{groupId}' => $group->id];

        require_once $this->sampleFile('groups/add_user.php', $replacements + ['{groupUserId}' => $groupUser->id]);

        /** @var $group \OpenStack\Identity\v3\Models\Group */
        require_once $this->sampleFile('groups/get_group.php', $replacements);
        self::assertInstanceOf(Models\Group::class, $group);

        /** @var $result bool */
        require_once $this->sampleFile('groups/check_user_membership.php', $replacements + ['{groupUserId}' => $groupUser->id]);
        self::assertTrue($result);

        require_once $this->sampleFile('groups/list_users.php', $replacements);

        require_once $this->sampleFile('groups/remove_user.php', $replacements + ['{groupUserId}' => $groupUser->id]);

        /** @var $result bool */
        require_once $this->sampleFile('groups/check_user_membership.php', $replacements + ['{groupUserId}' => $groupUser->id]);
        self::assertFalse($result);

        require_once $this->sampleFile('groups/update_group.php', $replacements + ['{name}' => $this->randomStr(), '{description}' => $this->randomStr()]);

        require_once $this->sampleFile('groups/list_groups.php', $replacements);

        require_once $this->sampleFile('groups/delete_group.php', $replacements);

        $groupUser->delete();
    }

    public function projects()
    {
        /** @var $project \OpenStack\Identity\v3\Models\Project */
        require_once $this->sampleFile('projects/add_project.php', ['{name}' => $this->randomStr(), '{description}' => $this->randomStr()]);
        self::assertInstanceOf(Models\Project::class, $project);

        $replacements = ['{id}' => $project->id];

        /** @var $project \OpenStack\Identity\v3\Models\Project */
        require_once $this->sampleFile('projects/get_project.php', $replacements);
        self::assertInstanceOf(Models\Project::class, $project);

        $domain = $this->getService()->createDomain(['name' => $this->randomStr()]);
        $parentRole = $this->getService()->createRole(['name' => $this->randomStr()]);
        $group = $this->getService()->createGroup(['name' => $this->randomStr(), 'domainId' => $domain->id]);

        require_once $this->sampleFile('projects/grant_group_role.php', $replacements + ['{groupId}' => $group->id, '{roleId}' => $parentRole->id]);

        /** @var $result bool */
        require_once $this->sampleFile('projects/check_group_role.php', $replacements + ['{groupId}' => $group->id, '{roleId}' => $parentRole->id]);
        self::assertTrue($result);

        require_once $this->sampleFile('projects/list_group_roles.php', $replacements + ['{groupId}' => $group->id]);

        require_once $this->sampleFile('projects/revoke_group_role.php', $replacements + ['{groupId}' => $group->id, '{roleId}' => $parentRole->id]);

        $group->delete();

        $user = $this->getService()->createUser(['name' => $this->randomStr(), 'domainId' => $domain->id]);

        require_once $this->sampleFile('projects/grant_user_role.php', $replacements + ['{projectUserId}' => $user->id, '{roleId}' => $parentRole->id]);

        /** @var $result bool */
        require_once $this->sampleFile('projects/check_user_role.php', $replacements + ['{projectUserId}' => $user->id, '{roleId}' => $parentRole->id]);
        self::assertTrue($result);

        require_once $this->sampleFile('projects/list_user_roles.php', $replacements + ['{projectUserId}' => $user->id]);

        require_once $this->sampleFile('projects/revoke_user_role.php', $replacements + ['{projectUserId}' => $user->id, '{roleId}' => $parentRole->id]);

        require_once $this->sampleFile('projects/update_project.php', $replacements);

        require_once $this->sampleFile('projects/delete_project.php', $replacements);

        $user->delete();
        $parentRole->delete();

        $domain->enabled = false;
        $domain->update();
        $domain->delete();
    }

    public function roles()
    {
        /** @var $role \OpenStack\Identity\v3\Models\Role */
        require_once $this->sampleFile('roles/add_role.php', ['{name}' => $this->randomStr()]);
        self::assertInstanceOf(Models\Role::class, $role);

        require_once $this->sampleFile('roles/list_roles.php', []);

        require_once $this->sampleFile('roles/list_assignments.php', []);
    }

    public function users()
    {
        $parentDomain  = $this->getService()->createDomain(['name' => $this->randomStr()]);
        $parentProject = $this->getService()->createProject(['name' => $this->randomStr(), 'domainId' => $parentDomain->id]);

        $replacements = [
            '{defaultProjectId}' => $parentProject->id,
            '{description}'      => $this->randomStr(),
            '{domainId}'         => $parentDomain->id,
            '{email}'            => 'foo@bar.com',
            '{enabled}'          => true,
            '{name}'             => $this->randomStr(),
            '{userPass}'         => $this->randomStr(),
        ];

        /** @var $user \OpenStack\Identity\v3\Models\User */
        require_once $this->sampleFile('users/add_user.php', $replacements);
        self::assertInstanceOf(Models\User::class, $user);

        $replacements = ['{id}' => $user->id];

        /** @var $user \OpenStack\Identity\v3\Models\User */
        require_once $this->sampleFile('users/get_user.php', $replacements);
        self::assertInstanceOf(Models\User::class, $user);

        require_once $this->sampleFile('users/list_users.php', []);

        require_once $this->sampleFile('users/list_groups.php', $replacements);

        require_once $this->sampleFile('users/list_projects.php', $replacements);

        /** @var $user \OpenStack\Identity\v3\Models\User */
        require_once $this->sampleFile('users/update_user.php', $replacements + ['{name}' => $this->randomStr(), '{description}' => $this->randomStr()]);
        self::assertInstanceOf(Models\User::class, $user);

        require_once $this->sampleFile('users/delete_user.php', $replacements);

        $parentProject->delete();

        $parentDomain->enabled = false;
        $parentDomain->update();
        $parentDomain->delete();
    }
}
