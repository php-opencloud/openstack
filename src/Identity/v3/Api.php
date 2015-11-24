<?php

namespace OpenStack\Identity\v3;

use OpenStack\Common\Api\AbstractApi;

class Api extends AbstractApi
{
    public function __construct()
    {
        $this->params = new Params();
    }

    public function postTokens()
    {
        return [
            'method' => 'POST',
            'path'   => 'auth/tokens',
            'params' => [
                'methods' => $this->params->methods(),
                'user'    => $this->params->user(),
                'tokenId' => $this->params->tokenBody(),
                'scope'   => $this->params->scope(),
            ]
        ];
    }

    public function getTokens()
    {
        return [
            'method' => 'GET',
            'path'   => 'auth/tokens',
            'params' => ['tokenId' => $this->params->tokenId()]
        ];
    }

    public function headTokens()
    {
        return [
            'method' => 'HEAD',
            'path'   => 'auth/tokens',
            'params' => ['tokenId' => $this->params->tokenId()]
        ];
    }

    public function deleteTokens()
    {
        return [
            'method' => 'DELETE',
            'path'   => 'auth/tokens',
            'params' => ['tokenId' => $this->params->tokenId()]
        ];
    }

    public function postServices()
    {
        return [
            'method'  => 'POST',
            'path'    => 'services',
            'jsonKey' => 'service',
            'params'  => [
                'name' => $this->params->name('service'),
                'type' => $this->params->type('service'),
            ]
        ];
    }

    public function getServices()
    {
        return [
            'method' => 'GET',
            'path'   => 'services',
            'params' => ['type' => $this->params->typeQuery()]
        ];
    }

    public function getService()
    {
        return [
            'method' => 'GET',
            'path'   => 'services/{id}',
            'params' => ['id' => $this->params->idUrl('service')]
        ];
    }

    public function patchService()
    {
        return [
            'method'  => 'PATCH',
            'path'    => 'services/{id}',
            'jsonKey' => 'service',
            'params'  => [
                'id'          => $this->params->idUrl('service'),
                'name'        => $this->params->name('service'),
                'type'        => $this->params->type('service'),
                'description' => $this->params->desc('service'),
            ]
        ];
    }

    public function deleteService()
    {
        return [
            'method' => 'DELETE',
            'path'   => 'services/{id}',
            'params' => ['id' => $this->params->idUrl('service')]
        ];
    }

    public function postEndpoints()
    {
        return [
            'method'  => 'POST',
            'path'    => 'endpoints',
            'jsonKey' => 'endpoint',
            'params'  => [
                'interface' => $this->params->interf(),
                'name'      => $this->isRequired($this->params->name('endpoint')),
                'region'    => $this->params->region(),
                'url'       => $this->params->endpointUrl(),
                'serviceId' => $this->params->serviceId(),
            ]
        ];
    }

    public function getEndpoints()
    {
        return [
            'method' => 'GET',
            'path'   => 'endpoints',
            'params' => [
                'interface' => $this->query($this->params->interf()),
                'serviceId' => $this->query($this->params->serviceId()),
            ]
        ];
    }

    public function patchEndpoint()
    {
        return [
            'method'  => 'PATCH',
            'path'    => 'endpoints/{id}',
            'jsonKey' => 'endpoint',
            'params'  => [
                'id'        => $this->params->idUrl('endpoint'),
                'interface' => $this->params->interf(),
                'name'      => $this->params->name('endpoint'),
                'region'    => $this->params->region(),
                'url'       => $this->params->endpointUrl(),
                'serviceId' => $this->params->serviceId(),
            ]
        ];
    }

    public function deleteEndpoint()
    {
        return [
            'method' => 'DELETE',
            'path'   => 'endpoints/{id}',
            'params' => ['id' => $this->params->idUrl('endpoint')]
        ];
    }

    public function postDomains()
    {
        return [
            'method'  => 'POST',
            'path'    => 'domains',
            'jsonKey' => 'domain',
            'params'  => [
                'name'        => $this->isRequired($this->params->name('domain')),
                'enabled'     => $this->params->enabled('domain'),
                'description' => $this->params->desc('domain'),
            ]
        ];
    }

    public function getDomains()
    {
        return [
            'method' => 'GET',
            'path'   => 'domains',
            'params' => [
                'name'    => $this->query($this->params->name('domain')),
                'enabled' => $this->query($this->params->enabled('domain')),
            ]
        ];
    }

    public function getDomain()
    {
        return [
            'method' => 'GET',
            'path'   => 'domains/{id}',
            'params' => ['id' => $this->params->idUrl('domain')]
        ];
    }

    public function patchDomain()
    {
        return [
            'method'  => 'PATCH',
            'path'    => 'domains/{id}',
            'jsonKey' => 'domain',
            'params'  => [
                'id'          => $this->params->idUrl('domain'),
                'name'        => $this->params->name('domain'),
                'enabled'     => $this->params->enabled('domain'),
                'description' => $this->params->desc('domain'),
            ]
        ];
    }

    public function deleteDomain()
    {
        return [
            'method' => 'DELETE',
            'path'   => 'domains/{id}',
            'params' => ['id' => $this->params->idUrl('domain')]
        ];
    }

    public function getUserRoles()
    {
        return [
            'method' => 'GET',
            'path'   => 'domains/{domainId}/users/{userId}/roles',
            'params' => [
                'domainId' => $this->params->idUrl('domain'),
                'userId'   => $this->params->idUrl('user'),
            ]
        ];
    }

    public function putUserRoles()
    {
        return [
            'method' => 'PUT',
            'path'   => 'domains/{domainId}/users/{userId}/roles/{roleId}',
            'params' => [
                'domainId' => $this->params->idUrl('domain'),
                'userId'   => $this->params->idUrl('user'),
                'roleId'   => $this->params->idUrl('role'),
            ]
        ];
    }

    public function headUserRole()
    {
        return [
            'method' => 'HEAD',
            'path'   => 'domains/{domainId}/users/{userId}/roles/{roleId}',
            'params' => [
                'domainId' => $this->params->idUrl('domain'),
                'userId'   => $this->params->idUrl('user'),
                'roleId'   => $this->params->idUrl('role'),
            ]
        ];
    }

    public function deleteUserRole()
    {
        return [
            'method' => 'DELETE',
            'path'   => 'domains/{domainId}/users/{userId}/roles/{roleId}',
            'params' => [
                'domainId' => $this->params->idUrl('domain'),
                'userId'   => $this->params->idUrl('user'),
                'roleId'   => $this->params->idUrl('role'),
            ]
        ];
    }

    public function getGroupRoles()
    {
        return [
            'method' => 'GET',
            'path'   => 'domains/{domainId}/groups/{groupId}/roles',
            'params' => [
                'domainId' => $this->params->idUrl('domain'),
                'groupId'  => $this->params->idUrl('group'),
            ]
        ];
    }

    public function putGroupRole()
    {
        return [
            'method' => 'PUT',
            'path'   => 'domains/{domainId}/groups/{groupId}/roles/{roleId}',
            'params' => [
                'domainId' => $this->params->idUrl('domain'),
                'groupId'  => $this->params->idUrl('group'),
                'roleId'   => $this->params->idUrl('role'),
            ]
        ];
    }

    public function headGroupRole()
    {
        return [
            'method' => 'HEAD',
            'path'   => 'domains/{domainId}/groups/{groupId}/roles/{roleId}',
            'params' => [
                'domainId' => $this->params->idUrl('domain'),
                'groupId'  => $this->params->idUrl('group'),
                'roleId'   => $this->params->idUrl('role'),
            ]
        ];
    }

    public function deleteGroupRole()
    {
        return [
            'method' => 'DELETE',
            'path'   => 'domains/{domainId}/groups/{groupId}/roles/{roleId}',
            'params' => [
                'domainId' => $this->params->idUrl('domain'),
                'groupId'  => $this->params->idUrl('group'),
                'roleId'   => $this->params->idUrl('role'),
            ]
        ];
    }

    public function postProjects()
    {
        return [
            'method'  => 'POST',
            'path'    => 'projects',
            'jsonKey' => 'project',
            'params'  => [
                'description' => $this->params->desc('project'),
                'domainId'    => $this->params->domainId('project'),
                'parentId'    => $this->params->parentId(),
                'enabled'     => $this->params->enabled('project'),
                'name'        => $this->isRequired($this->params->name('project'))
            ]
        ];
    }

    public function getProjects()
    {
        return [
            'method' => 'GET',
            'path'   => 'projects',
            'params' => [
                'domainId' => $this->query($this->params->domainId('project')),
                'enabled'  => $this->query($this->params->enabled('project')),
                'name'     => $this->query($this->params->name('project')),
            ]
        ];
    }

    public function getProject()
    {
        return [
            'method' => 'GET',
            'path'   => 'projects/{id}',
            'params' => ['id' => $this->params->idUrl('project')]
        ];
    }

    public function patchProject()
    {
        return [
            'method'  => 'PATCH',
            'path'    => 'projects/{id}',
            'jsonKey' => 'project',
            'params'  => [
                'id'          => $this->params->idUrl('project'),
                'description' => $this->params->desc('project'),
                'domainId'    => $this->params->domainId('project'),
                'parentId'    => $this->params->parentId(),
                'enabled'     => $this->params->enabled('project'),
                'name'        => $this->params->name('project'),
            ]
        ];
    }

    public function deleteProject()
    {
        return [
            'method' => 'DELETE',
            'path'   => 'projects/{id}',
            'params' => ['id' => $this->params->idUrl('project')]
        ];
    }

    public function getProjectUserRoles()
    {
        return [
            'method' => 'GET',
            'path'   => 'projects/{projectId}/users/{userId}/roles',
            'params' => [
                'projectId' => $this->params->idUrl('project'),
                'userId'    => $this->params->idUrl('user'),
            ]
        ];
    }

    public function putProjectUserRole()
    {
        return [
            'method' => 'PUT',
            'path'   => 'projects/{projectId}/users/{userId}/roles/{roleId}',
            'params' => [
                'projectId' => $this->params->idUrl('project'),
                'userId'    => $this->params->idUrl('user'),
                'roleId'    => $this->params->idUrl('role'),
            ]
        ];
    }

    public function headProjectUserRole()
    {
        return [
            'method' => 'HEAD',
            'path'   => 'projects/{projectId}/users/{userId}/roles/{roleId}',
            'params' => [
                'projectId' => $this->params->idUrl('project'),
                'userId'    => $this->params->idUrl('user'),
                'roleId'    => $this->params->idUrl('role'),
            ]
        ];
    }

    public function deleteProjectUserRole()
    {
        return [
            'method' => 'DELETE',
            'path'   => 'projects/{projectId}/users/{userId}/roles/{roleId}',
            'params' => [
                'projectId' => $this->params->idUrl('project'),
                'userId'    => $this->params->idUrl('user'),
                'roleId'    => $this->params->idUrl('role'),
            ]
        ];
    }

    public function getProjectGroupRoles()
    {
        return [
            'method' => 'GET',
            'path'   => 'projects/{projectId}/groups/{groupId}/roles',
            'params' => [
                'projectId' => $this->params->idUrl('project'),
                'groupId'   => $this->params->idUrl('group'),
            ]
        ];
    }

    public function putProjectGroupRole()
    {
        return [
            'method' => 'PUT',
            'path'   => 'projects/{projectId}/groups/{groupId}/roles/{roleId}',
            'params' => [
                'projectId' => $this->params->idUrl('project'),
                'groupId'   => $this->params->idUrl('group'),
                'roleId'    => $this->params->idUrl('role'),
            ]
        ];
    }

    public function headProjectGroupRole()
    {
        return [
            'method' => 'HEAD',
            'path'   => 'projects/{projectId}/groups/{groupId}/roles/{roleId}',
            'params' => [
                'projectId' => $this->params->idUrl('project'),
                'groupId'   => $this->params->idUrl('group'),
                'roleId'    => $this->params->idUrl('role'),
            ]
        ];
    }

    public function deleteProjectGroupRole()
    {
        return [
            'method' => 'DELETE',
            'path'   => 'projects/{projectId}/groups/{groupId}/roles/{roleId}',
            'params' => [
                'projectId' => $this->params->idUrl('project'),
                'groupId'   => $this->params->idUrl('group'),
                'roleId'    => $this->params->idUrl('role'),
            ]
        ];
    }

    public function postUsers()
    {
        return [
            'method'  => 'POST',
            'path'    => 'users',
            'jsonKey' => 'user',
            'params'  => [
                'defaultProjectId' => $this->params->defaultProjectId(),
                'description'      => $this->params->desc('user'),
                'domainId'         => $this->params->domainId('user'),
                'email'            => $this->params->email(),
                'enabled'          => $this->params->enabled('user'),
                'name'             => $this->isRequired($this->params->name('user')),
                'password'         => $this->params->password(),
            ]
        ];
    }

    public function getUsers()
    {
        return [
            'method' => 'GET',
            'path'   => 'users',
            'params' => [
                'domainId' => $this->query($this->params->domainId('user')),
                'enabled'  => $this->query($this->params->enabled('user')),
                'name'     => $this->query($this->params->name('user')),
            ]
        ];
    }

    public function getUser()
    {
        return [
            'method' => 'GET',
            'path'   => 'users/{id}',
            'params' => ['id' => $this->params->idUrl('user')]
        ];
    }

    public function patchUser()
    {
        return [
            'method'  => 'PATCH',
            'path'    => 'users/{id}',
            'jsonKey' => 'user',
            'params'  => [
                'id'               => $this->params->idUrl('user'),
                'defaultProjectId' => $this->params->defaultProjectId(),
                'description'      => $this->params->desc('user'),
                'email'            => $this->params->email(),
                'enabled'          => $this->params->enabled('user'),
            ]
        ];
    }

    public function deleteUser()
    {
        return [
            'method' => 'DELETE',
            'path'   => 'users/{id}',
            'params' => ['id' => $this->params->idUrl('user')]
        ];
    }

    public function getUserGroups()
    {
        return [
            'method' => 'GET',
            'path'   => 'users/{id}/groups',
            'params' => ['id' => $this->params->idUrl('user')]
        ];
    }

    public function getUserProjects()
    {
        return [
            'method' => 'GET',
            'path'   => 'users/{id}/projects',
            'params' => ['id' => $this->params->idUrl('user')]
        ];
    }

    public function postGroups()
    {
        return [
            'method'  => 'POST',
            'path'    => 'groups',
            'jsonKey' => 'group',
            'params'  => [
                'description' => $this->params->desc('group'),
                'domainId'    => $this->params->domainId('group'),
                'name'        => $this->params->name('group')
            ]
        ];
    }

    public function getGroups()
    {
        return [
            'method' => 'GET',
            'path'   => 'groups',
            'params' => ['domainId' => $this->query($this->params->domainId('group'))]
        ];
    }

    public function getGroup()
    {
        return [
            'method' => 'GET',
            'path'   => 'groups/{id}',
            'params' => ['id' => $this->params->idUrl('group')]
        ];
    }

    public function patchGroup()
    {
        return [
            'method'  => 'PATCH',
            'path'    => 'groups/{id}',
            'jsonKey' => 'group',
            'params'  => [
                'id'          => $this->params->idUrl('group'),
                'description' => $this->params->desc('group'),
                'name'        => $this->params->name('group')
            ]
        ];
    }

    public function deleteGroup()
    {
        return [
            'method' => 'DELETE',
            'path'   => 'groups/{id}',
            'params' => ['id' => $this->params->idUrl('group')]
        ];
    }

    public function getGroupUsers()
    {
        return [
            'method' => 'GET',
            'path'   => 'groups/{id}/users',
            'params' => ['id' => $this->params->idUrl('group')]
        ];
    }

    public function putGroupUser()
    {
        return [
            'method' => 'PUT',
            'path'   => 'groups/{groupId}/users/{userId}',
            'params' => [
                'groupId' => $this->params->idUrl('group'),
                'userId'  => $this->params->idUrl('user'),
            ]
        ];
    }

    public function deleteGroupUser()
    {
        return [
            'method' => 'DELETE',
            'path'   => 'groups/{groupId}/users/{userId}',
            'params' => [
                'groupId' => $this->params->idUrl('group'),
                'userId'  => $this->params->idUrl('user'),
            ]
        ];
    }

    public function headGroupUser()
    {
        return [
            'method' => 'HEAD',
            'path'   => 'groups/{groupId}/users/{userId}',
            'params' => [
                'groupId' => $this->params->idUrl('group'),
                'userId'  => $this->params->idUrl('user'),
            ]
        ];
    }

    public function postCredentials()
    {
        return [
            'method' => 'POST',
            'path'   => 'credentials',
            'params' => [
                'blob'      => $this->params->blob(),
                'projectId' => $this->params->projectId(),
                'type'      => $this->params->type('credential'),
                'userId'    => $this->params->userId(),
            ]
        ];
    }

    public function getCredentials()
    {
        return [
            'method' => 'GET',
            'path'   => 'credentials',
            'params' => []
        ];
    }

    public function getCredential()
    {
        return [
            'method' => 'GET',
            'path'   => 'credentials/{id}',
            'params' => ['id' => $this->params->idUrl('credential')]
        ];
    }

    public function patchCredential()
    {
        return [
            'method' => 'PATCH',
            'path'   => 'credentials/{id}',
            'params' => ['id' => $this->params->idUrl('credential')] + $this->postCredentials()['params']
        ];
    }

    public function deleteCredential()
    {
        return [
            'method' => 'DELETE',
            'path'   => 'credentials/{id}',
            'params' => ['id' => $this->params->idUrl('credential')]
        ];
    }

    public function postRoles()
    {
        return [
            'method'  => 'POST',
            'path'    => 'roles',
            'jsonKey' => 'role',
            'params'  => ['name' => $this->isRequired($this->params->name('role'))]
        ];
    }

    public function getRoles()
    {
        return [
            'method' => 'GET',
            'path'   => 'roles',
            'params' => ['name' => $this->query($this->params->name('role'))]
        ];
    }

    public function deleteRole()
    {
        return [
            'method' => 'DELETE',
            'path'   => 'roles/{id}',
            'params' => ['id' => $this->params->idUrl('role')]
        ];
    }

    public function getRoleAssignments()
    {
        return [
            'method' => 'GET',
            'path'   => 'role_assignments',
            'params' => [
                'userId'    => $this->params->userIdQuery(),
                'groupId'   => $this->params->groupIdQuery(),
                'roleId'    => $this->params->roleIdQuery(),
                'domainId'  => $this->params->domainIdQuery(),
                'projectId' => $this->params->projectIdQuery(),
                'effective' => $this->params->effective(),
            ]
        ];
    }

    public function postPolicies()
    {
        return [
            'method' => 'POST',
            'path'   => 'policies',
            'params' => [
                'blob'      => $this->params->blob(),
                'projectId' => $this->params->projectId('policy'),
                'type'      => $this->params->type('policy'),
                'userId'    => $this->params->userId('policy')
            ]
        ];
    }

    public function getPolicies()
    {
        return [
            'method' => 'GET',
            'path'   => 'policies',
            'params' => ['type' => $this->query($this->params->type('policy'))]
        ];
    }

    public function getPolicy()
    {
        return [
            'method' => 'GET',
            'path'   => 'policies/{id}',
            'params' => ['id' => $this->params->idUrl('policy')]
        ];
    }

    public function patchPolicy()
    {
        return [
            'method' => 'PATCH',
            'path'   => 'policies/{id}',
            'params' => [
                'id'        => $this->params->idUrl('policy'),
                'blob'      => $this->params->blob(),
                'projectId' => $this->params->projectId('policy'),
                'type'      => $this->params->type('policy'),
                'userId'    => $this->params->userId(),
            ]
        ];
    }

    public function deletePolicy()
    {
        return [
            'method' => 'DELETE',
            'path'   => 'policies/{id}',
            'params' => ['id' => $this->params->idUrl('policy')]
        ];
    }
}
