<?php

namespace OpenStack\Identity\v3;

use OpenStack\Common\Api\ApiInterface;

class Api implements ApiInterface
{
    private function domainParam()
    {
        return [
            'type' => 'object',
            'params' => [
                'id'   => ['type' => 'string'],
                'name' => ['type' => 'string']
            ]
        ];
    }

    private function projectParam()
    {
        return [
            'type' => 'object',
            'params' => [
                'id'     => ['type' => 'string'],
                'name'   => ['type' => 'string'],
                'domain' => $this->domainParam(),
            ]
        ];
    }

    private $idUrlParam = [
        'required' => true,
        'location' => 'url',
        'type' => 'string',
    ];

    private $tokenIdParam = [
        'type'     => 'string',
        'location' => 'header',
        'sentAs'   => 'X-Subject-Token'
    ];

    public function postTokens()
    {
        return [
            'method' => 'POST',
            'path'   => 'auth/tokens',
            'params' => [
                'methods' => [
                    'type' => 'array',
                    'path' => 'auth.identity',
                    'items' => ['type' => 'string']
                ],
                'user' => [
                    'type'   => 'object',
                    'path'   => 'auth.identity.password',
                    'properties' => [
                        'id'       => ['type' => 'string'],
                        'name'     => ['type' => 'string'],
                        'password' => ['type' => 'string'],
                        'domain'   => $this->domainParam()
                    ]
                ],
                'tokenId' => [
                    'type'   => 'string',
                    'path'   => 'auth.identity.token',
                    'sentAs' => 'id',
                ],
                'scope' => [
                    'type' => 'object',
                    'path' => 'auth',
                    'properties' => [
                        'project' => $this->projectParam(),
                        'domain'  => $this->domainParam()
                    ]
                ]
            ]
        ];
    }

    public function getTokens()
    {
        return [
            'method' => 'GET',
            'path'   => 'auth/tokens',
            'params' => ['tokenId' => $this->tokenIdParam]
        ];
    }

    public function headTokens()
    {
        return [
            'method' => 'HEAD',
            'path'   => 'auth/tokens',
            'params' => ['tokenId' => $this->tokenIdParam]
        ];
    }

    public function deleteTokens()
    {
        return [
            'method' => 'DELETE',
            'path'   => 'auth/tokens',
            'params' => ['tokenId' => $this->tokenIdParam]
        ];
    }

    public function postServices()
    {
        return [
            'method'  => 'POST',
            'path'    => 'services',
            'jsonKey' => 'service',
            'params' => [
                'name' => ['type' => 'string'],
                'type' => ['type' => 'string']
            ]
        ];
    }

    public function getServices()
    {
        return [
            'method' => 'GET',
            'path'   => 'services',
            'params' => [
                'type' => [
                    'type'     => 'string',
                    'location' => 'query'
                ]
            ]
        ];
    }

    public function getService()
    {
        return [
            'method' => 'GET',
            'path'   => 'services/{id}',
            'params' => ['id' => $this->idUrlParam]
        ];
    }

    public function patchService()
    {
        return [
            'method' => 'PATCH',
            'path'   => 'services/{id}',
            'params' => [
                'id' => $this->idUrlParam,
                'name' => ['type' => 'string'],
                'type' => ['type' => 'string']
            ]
        ];
    }

    public function deleteService()
    {
        return [
            'method' => 'DELETE',
            'path'   => 'services/{id}',
            'params' => ['id' => $this->idUrlParam]
        ];
    }

    public function postEndpoints()
    {
        return [
            'method' => 'POST',
            'path'   => 'endpoints',
            'jsonKey' => 'endpoint',
            'params' => [
                'interface' => [
                    'type' => 'string',
                ],
                'name' => [
                    'type' => 'string',
                    'required' => true,
                ],
                'region' => [
                    'type' => 'string',
                ],
                'url' => [
                    'type' => 'string',
                ],
                'serviceId' => [
                    'type' => 'string',
                    'sentAs' => 'service_id',
                ],
            ]
        ];
    }

    public function getEndpoints()
    {
        return [
            'method' => 'GET',
            'path'   => 'endpoints',
            'params' => [
                'interface' => [
                    'type' => 'string',
                    'location' => 'query'
                ],
                'serviceId' => [
                    'type' => 'string',
                    'sentAs' => 'service_id',
                    'location' => 'query'
                ],
            ]
        ];
    }

    public function patchEndpoints()
    {
        return [
            'method' => 'PATCH',
            'path'   => 'endpoints/{id}',
            'params' => [
                'id' => $this->idUrlParam,
                'interface' => [
                    'type' => 'string',
                ],
                'name' => [
                    'type' => 'string',
                ],
                'region' => [
                    'type' => 'string',
                ],
                'url' => [
                    'type' => 'string',
                ],
                'serviceId' => [
                    'type' => 'string',
                    'sentAs' => 'service_id',
                ],
            ]
        ];
    }

    public function deleteEndpoints()
    {
        return [
            'method' => 'DELETE',
            'path'   => 'endpoints/{id}',
            'params' => ['id' => $this->idUrlParam,]
        ];
    }

    public function postDomains()
    {
        return [
            'method' => 'POST',
            'path'   => 'domains',
            'jsonKey' => 'domain',
            'params' => [
                'name' => [
                    'type' => 'string',
                    'required' => true,
                ],
                'enabled' => [
                    'type' => 'boolean',
                ],
                'description' => [
                    'type' => 'string',
                ]
            ]
        ];
    }

    public function getDomains()
    {
        return [
            'method' => 'GET',
            'path'   => 'domains',
            'params' => [
                'name' => [
                    'type' => 'string',
                    'location' => 'query',
                ],
                'enabled' => [
                    'type' => 'boolean',
                    'location' => 'query',
                ],
            ]
        ];
    }

    public function getDomain()
    {
        return [
            'method' => 'GET',
            'path'   => 'domains/{id}',
            'params' => ['id' => $this->idUrlParam]
        ];
    }

    public function patchDomain()
    {
        return [
            'method' => 'PATCH',
            'path'   => 'domains/{id}',
            'jsonKey' => 'domain',
            'params' => [
                'id' => $this->idUrlParam,
                'name' => [
                    'type' => 'string',
                ],
                'enabled' => [
                    'type' => 'boolean',
                ],
                'description' => [
                    'type' => 'string',
                ]
            ]
        ];
    }

    public function deleteDomain()
    {
        return [
            'method' => 'DELETE',
            'path'   => 'domains/{id}',
            'params' => ['id' => $this->idUrlParam]
        ];
    }

    public function getUserRoles()
    {
        return [
            'method' => 'GET',
            'path'   => 'domains/{domainId}/users/{userId}/roles',
            'params' => [
                'domainId' => $this->idUrlParam,
                'userId'   => $this->idUrlParam
            ]
        ];
    }

    public function putUserRoles()
    {
        return [
            'method' => 'PUT',
            'path'   => 'domains/{domainId}/users/{userId}/roles/{roleId}',
            'params' => [
                'domainId' => $this->idUrlParam,
                'userId'   => $this->idUrlParam,
                'roleId'   => $this->idUrlParam,
            ]
        ];
    }

    public function headUserRole()
    {
        return [
            'method' => 'HEAD',
            'path'   => 'domains/{domainId}/users/{userId}/roles/{roleId}',
            'params' => [
                'domainId' => $this->idUrlParam,
                'userId'   => $this->idUrlParam,
                'roleId'   => $this->idUrlParam,
            ]
        ];
    }

    public function deleteUserRole()
    {
        return [
            'method' => 'DELETE',
            'path'   => 'domains/{domainId}/users/{userId}/roles/{roleId}',
            'params' => [
                'domainId' => $this->idUrlParam,
                'userId'   => $this->idUrlParam,
                'roleId'   => $this->idUrlParam,
            ]
        ];
    }

    public function getGroupRoles()
    {
        return [
            'method' => 'GET',
            'path'   => 'domains/{domainId}/groups/{groupId}/roles',
            'params' => [
                'domainId' => $this->idUrlParam,
                'groupId'  => $this->idUrlParam,
            ]
        ];
    }

    public function putGroupRole()
    {
        return [
            'method' => 'PUT',
            'path'   => 'domains/{domainId}/groups/{groupId}/roles/{roleId}',
            'params' => [
                'domainId' => $this->idUrlParam,
                'groupId'  => $this->idUrlParam,
                'roleId'   => $this->idUrlParam
            ]
        ];
    }

    public function headGroupRole()
    {
        return [
            'method' => 'POST',
            'path'   => 'domains/{domainId}/groups/{groupId}/roles/{roleId}',
            'params' => [
                'domainId' => $this->idUrlParam,
                'groupId'  => $this->idUrlParam,
                'roleId'   => $this->idUrlParam
            ]
        ];
    }

    public function deleteGroupRole()
    {
        return [
            'method' => 'DELETE',
            'path'   => 'domains/{domainId}/groups/{groupId}/roles/{roleId}',
            'params' => [
                'domainId' => $this->idUrlParam,
                'groupId'  => $this->idUrlParam,
                'roleId'   => $this->idUrlParam
            ]
        ];
    }

    public function postProjects()
    {
        return [
            'method' => 'POST',
            'path'   => 'projects',
            'params' => [
                'description' => [
                    'type' => 'string'
                ],
                'domainId' => [
                    'type' => 'string',
                    'sentAs' => 'domain_id'
                ],
                'enabled' => [
                    'type' => 'boolean'
                ],
                'name' => [
                    'type' => 'string',
                    'required' => true,
                ]
            ]
        ];
    }

    public function getProjects()
    {
        return [
            'method' => 'GET',
            'path'   => 'projects',
            'params' => [
                'domainId' => [
                    'type' => 'string',
                    'sentAs' => 'domain_id',
                    'location' => 'query'
                ],
                'enabled' => [
                    'type' => 'boolean',
                    'location' => 'query'
                ],
                'name' => [
                    'type' => 'string',
                    'location' => 'query'
                ]
            ]
        ];
    }

    public function getProject()
    {
        return [
            'method' => 'GET',
            'path'   => 'projects/{id}',
            'params' => ['id' => $this->idUrlParam]
        ];
    }

    public function patchProject()
    {
        return [
            'method' => 'PATCH',
            'path'   => 'projects/{id}',
            'params' => [
                'id' => $this->idUrlParam,
                'description' => [
                    'type' => 'string'
                ],
                'domainId' => [
                    'type' => 'string',
                    'sentAs' => 'domain_id'
                ],
                'enabled' => [
                    'type' => 'boolean'
                ],
                'name' => [
                    'type' => 'string',
                ]
            ]
        ];
    }

    public function deleteProject()
    {
        return [
            'method' => 'DELETE',
            'path'   => 'projects/{id}',
            'params' => ['id' => $this->idUrlParam]
        ];
    }

    public function getProjectUserRoles()
    {
        return [
            'method' => 'GET',
            'path'   => 'projects/{projectId}/users/{userId}/roles',
            'params' => [
                'projectId' => $this->idUrlParam,
                'userId'    => $this->idUrlParam
            ]
        ];
    }

    public function putProjectUserRole()
    {
        return [
            'method' => 'PUT',
            'path'   => 'projects/{projectId}/users/{userId}/roles/{roleId}',
            'params' => [
                'projectId' => $this->idUrlParam,
                'userId'    => $this->idUrlParam,
                'roleId'    => $this->idUrlParam
            ]
        ];
    }

    public function headProjectUserRole()
    {
        return [
            'method' => 'HEAD',
            'path'   => 'projects/{projectId}/users/{userId}/roles/{roleId}',
            'params' => [
                'projectId' => $this->idUrlParam,
                'userId'    => $this->idUrlParam,
                'roleId'    => $this->idUrlParam
            ]
        ];
    }

    public function deleteProjectUserRole()
    {
        return [
            'method' => 'DELETE',
            'path'   => 'projects/{projectId}/users/{userId}/roles/{roleId}',
            'params' => [
                'projectId' => $this->idUrlParam,
                'userId'    => $this->idUrlParam,
                'roleId'    => $this->idUrlParam
            ]
        ];
    }

    public function getProjectGroupRoles()
    {
        return [
            'method' => 'GET',
            'path'   => 'projects/{projectId}/groups/{groupId}/roles',
            'params' => [
                'projectId' => $this->idUrlParam,
                'groupId'   => $this->idUrlParam
            ]
        ];
    }

    public function putProjectGroupRole()
    {
        return [
            'method' => 'PUT',
            'path'   => 'projects/{projectId}/groups/{groupId}/roles/{roleId}',
            'params' => [
                'projectId' => $this->idUrlParam,
                'groupId'   => $this->idUrlParam,
                'roleId'    => $this->idUrlParam
            ]
        ];
    }

    public function headProjectGroupRole()
    {
        return [
            'method' => 'HEAD',
            'path'   => 'projects/{projectId}/groups/{groupId}/roles/{roleId}',
            'params' => [
                'projectId' => $this->idUrlParam,
                'groupId'   => $this->idUrlParam,
                'roleId'    => $this->idUrlParam
            ]
        ];
    }

    public function deleteProjectGroupRole()
    {
        return [
            'method' => 'DELETE',
            'path'   => 'projects/{projectId}/groups/{groupId}/roles/{roleId}',
            'params' => [
                'projectId' => $this->idUrlParam,
                'groupId'   => $this->idUrlParam,
                'roleId'    => $this->idUrlParam
            ]
        ];
    }

    public function postUsers()
    {
        return [
            'method' => 'POST',
            'path'   => 'users',
            'jsonKey' => 'user',
            'params' => [
                'defaultProjectId' => [
                    'sentAs' => 'default_project_id',
                    'type'   => 'string'
                ],
                'description' => [
                    'type' => 'string'
                ],
                'domainId' => [
                    'type' => 'string',
                    'sentAs' => 'domain_id'
                ],
                'email' => [
                    'type' => 'string'
                ],
                'enabled' => [
                    'type' => 'boolean'
                ],
                'name' => [
                    'type' => 'string',
                    'required' => true
                ],
                'password' => [
                    'type' => 'string'
                ]
            ]
        ];
    }

    public function getUsers()
    {
        return [
            'method' => 'GET',
            'path'   => 'users',
            'params' => [
                'domainId' => [
                    'type' => 'string',
                    'sentAs' => 'domain_id',
                    'location' => 'query'
                ],
                'enabled' => [
                    'type' => 'boolean',
                    'location' => 'query'
                ],
                'name' => [
                    'type' => 'string',
                    'location' => 'query'
                ],
            ]
        ];
    }

    public function getUser()
    {
        return [
            'method' => 'GET',
            'path'   => 'users/{id}',
            'params' => ['id' => $this->idUrlParam]
        ];
    }

    public function patchUser()
    {
        return [
            'method' => 'PATCH',
            'path'   => 'users/{id}',
            'jsonKey' => 'user',
            'params' => [
                'id' => $this->idUrlParam,
                'defaultProjectId' => [
                    'sentAs' => 'default_project_id',
                    'type'   => 'string'
                ],
                'description' => [
                    'type' => 'string'
                ],
                'email' => [
                    'type' => 'string'
                ],
                'enabled' => [
                    'type' => 'boolean'
                ],
            ]
        ];
    }

    public function deleteUser()
    {
        return [
            'method' => 'DELETE',
            'path'   => 'users/{id}',
            'params' => ['id' => $this->idUrlParam]
        ];
    }

    public function getUserGroups()
    {
        return [
            'method' => 'GET',
            'path'   => 'users/{id}/groups',
            'params' => ['id' => $this->idUrlParam]
        ];
    }

    public function getUserProjects()
    {
        return [
            'method' => 'GET',
            'path'   =>'users/{id}/projects',
            'params' => ['id' => $this->idUrlParam]
        ];
    }

    public function postGroups()
    {
        return [
            'method' => 'POST',
            'path'   => 'groups',
            'jsonKey' => 'group',
            'params' => [
                'description' => [
                    'type' => 'string',
                ],
                'domainId' => [
                    'type' => 'string',
                    'sentAs' => 'domain_id',
                ],
                'name' => [
                    'type' => 'string',
                    'required' => true
                ]
            ]
        ];
    }

    public function getGroups()
    {
        return [
            'method' => 'GET',
            'path'   => 'groups',
            'params' => [
                'domainId' => [
                    'type' => 'string',
                    'sentAs' => 'domain_id',
                    'location' => 'query',
                ],
            ]
        ];
    }

    public function getGroup()
    {
        return [
            'method' => 'GET',
            'path'   => 'groups/{id}',
            'params' => ['id' => $this->idUrlParam]
        ];
    }

    public function patchGroup()
    {
        return [
            'method' => 'PATCH',
            'path'   => 'groups/{id}',
            'params' => [
                'id' => $this->idUrlParam,
                'description' => [
                    'type' => 'string',
                ],
                'name' => [
                    'type' => 'string',
                ]
            ]
        ];
    }

    public function deleteGroup()
    {
        return [
            'method' => 'DELETE',
            'path'   => 'groups/{id}',
            'params' => ['id' => $this->idUrlParam]
        ];
    }

    public function getGroupUsers()
    {
        return [
            'method' => 'GET',
            'path'   => 'groups/{id}/users',
            'params' => ['id' => $this->idUrlParam]
        ];
    }

    public function putGroupUser()
    {
        return [
            'method' => 'PUT',
            'path'   => 'groups/{groupId}/users/{userId}',
            'params' => [
                'groupId' => $this->idUrlParam,
                'userId'  => $this->idUrlParam,
            ]
        ];
    }

    public function deleteGroupUser()
    {
        return [
            'method' => 'DELETE',
            'path'   => 'groups/{groupId}/users/{userId}',
            'params' => [
                'groupId' => $this->idUrlParam,
                'userId'  => $this->idUrlParam,
            ]
        ];
    }

    public function headGroupUser()
    {
        return [
            'method' => 'GET',
            'path'   => 'groups/{groupId}/users/{userId}',
            'params' => [
                'groupId' => $this->idUrlParam,
                'userId'  => $this->idUrlParam,
            ]
        ];
    }

    public function postCredentials()
    {
        return [
            'method' => 'POST',
            'path'   => 'credentials',
            'params' => [
                'blob' => [
                    'type' => 'string'
                ],
                'projectId' => [
                    'type' => 'string',
                    'sentAs' => 'project_id',
                ],
                'type' => [
                    'type' => 'string'
                ],
                'userId' => [
                    'type' => 'string',
                    'sentAs' => 'user_id',
                ],
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
            'params' => ['id' => $this->idUrlParam]
        ];
    }

    public function patchCredential()
    {
        return [
            'method' => 'PATCH',
            'path'   => 'credentials/{id}',
            'params' => ['id' => $this->idUrlParam]
        ];
    }

    public function deleteCredential()
    {
        return [
            'method' => 'DELETE',
            'path'   => 'credentials/{id}',
            'params' => ['id' => $this->idUrlParam]
        ];
    }

    public function postRoles()
    {
        return [
            'method' => 'POST',
            'path'   => 'roles',
            'jsonKey' => 'role',
            'params' => [
                'name' => [
                    'type' => 'string',
                    'required' => true
                ]
            ]
        ];
    }

    public function getRoles()
    {
        return [
            'method' => 'GET',
            'path'   => 'roles',
            'params' => [
                'name' => [
                    'type' => 'string',
                    'location' => 'query'
                ]
            ]
        ];
    }

    public function getRoleAssignments()
    {
        return [
            'method' => 'GET',
            'path'   => 'role_assignments',
            'params' => [
                'userId' => [
                    'sentAs' => 'user.id',
                    'location' => 'query'
                ],
                'groupId' => [
                    'sentAs' => 'group.id',
                    'location' => 'query'
                ],
                'roleId' => [
                    'sentAs' => 'role.id',
                    'location' => 'query'
                ],
                'domainId' => [
                    'sentAs' => 'scope.domain.id',
                    'location' => 'query'
                ],
                'projectId' => [
                    'sentAs' => 'scope.project.id',
                    'location' => 'query'
                ],
                'effective' => [
                    'type' => 'boolean',
                    'location' => 'query'
                ]
            ]
        ];
    }

    public function postPolicies()
    {
        return [
            'method' => 'POST',
            'path'   => 'policies',
            'params' => [
                'blob' => [
                    'type' => 'string',
                ],
                'projectId' => [
                    'type' => 'string',
                    'sentAs' => 'project_id'
                ],
                'type' => [
                    'type' => 'string',
                ],
                'userId' => [
                    'type' => 'string',
                    'sentAs' => 'user_id'
                ]
            ]
        ];
    }

    public function getPolicies()
    {
        return [
            'method' => 'GET',
            'path'   => 'policies',
            'params' => [
                'type' => [
                    'type' => 'string',
                    'location' => 'query'
                ]
            ]
        ];
    }

    public function getPolicy()
    {
        return [
            'method' => 'GET',
            'path'   => 'policies/{id}',
            'params' => ['id' => $this->idUrlParam]
        ];
    }

    public function patchPolicy()
    {
        return [
            'method' => 'PATCH',
            'path'   => 'policies/{id}',
            'params' => [
                'id' => $this->idUrlParam,
                'blob' => [
                    'type' => 'string',
                ],
                'projectId' => [
                    'type' => 'string',
                    'sentAs' => 'project_id'
                ],
                'type' => [
                    'type' => 'string',
                ],
                'userId' => [
                    'type' => 'string',
                    'sentAs' => 'user_id'
                ]
            ]
        ];
    }

    public function deletePolicy()
    {
        return [
            'method' => 'DELETE',
            'path'   => 'policies/{id}',
            'params' => ['id' => $this->idUrlParam]
        ];
    }
}