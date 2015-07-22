<?php

namespace OpenStack\Identity\v3;

use OpenStack\Common\Api\AbstractApi;

class Api extends AbstractApi
{
    private function domainParam()
    {
        return [
            'type' => 'object',
            'properties' => [
                'id'   => ['type' => 'string', 'description' => Desc::id('domain')],
                'name' => ['type' => 'string', 'description' => 'The name of the domain'],
            ]
        ];
    }

    private function projectParam()
    {
        return [
            'type' => 'object',
            'properties' => [
                'id'     => ['type' => 'string', 'description' => Desc::id('project')],
                'name'   => ['type' => 'string', 'description' => 'The name of the project'],
                'domain' => $this->domainParam(),
            ]
        ];
    }

    private $idUrlParam = [
        'required' => true,
        'location' => 'url',
        'type' => 'string',
        'description' => 'The unique ID'
    ];

    private $tokenIdParam = [
        'type'     => 'string',
        'location' => 'header',
        'sentAs'   => 'X-Subject-Token',
        'description' => 'The unique token ID'
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
                    'description' => Desc::$methods,
                    'items' => ['type' => 'string']
                ],
                'user' => [
                    'type'   => 'object',
                    'path'   => 'auth.identity.password',
                    'properties' => [
                        'id'       => ['type' => 'string', 'description' => Desc::id('user')],
                        'name'     => ['type' => 'string', 'description' => 'The username of the user'],
                        'password' => ['type' => 'string', 'description' => 'The password of the user'],
                        'domain'   => $this->domainParam()
                    ]
                ],
                'tokenId' => [
                    'type'   => 'string',
                    'path'   => 'auth.identity.token',
                    'sentAs' => 'id',
                    'description' => Desc::id('token'),
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
                'name' => ['type' => 'string', 'description' => 'The name of the new service, as it will appear in the catalog'],
                'type' => ['type' => 'string', 'description' => 'The type of the new service, as it will appear in the catalog']
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
                    'location' => 'query',
                    'description' => 'Filters all the available services according to a given type'
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
            'jsonKey' => 'service',
            'params' => [
                'id' => $this->idUrlParam,
                'name' => ['type' => 'string', 'description' => Desc::name('service')],
                'type' => ['type' => 'string', 'description' => Desc::type('service')],
                'description' => ['type' => 'string', 'description' => 'A brief summary which explains what the service does'],
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
                    'description' => Desc::$interface,
                ],
                'name' => [
                    'type' => 'string',
                    'required' => true,
                    'description' => Desc::id('endpoint')
                ],
                'region' => [
                    'type' => 'string',
                    'description' => Desc::$region,
                ],
                'url' => [
                    'type' => 'string',
                    'description' => Desc::$endpointUrl,
                ],
                'serviceId' => [
                    'type' => 'string',
                    'sentAs' => 'service_id',
                    'description' => Desc::id('service') . ' that this endpoint belongs to',
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
                    'location' => 'query',
                    'description' => Desc::$interface,
                ],
                'serviceId' => [
                    'type' => 'string',
                    'sentAs' => 'service_id',
                    'location' => 'query',
                    'description' => Desc::id('service') . ' that this endpoint belongs to',
                ],
            ]
        ];
    }

    public function patchEndpoint()
    {
        return [
            'method' => 'PATCH',
            'path'   => 'endpoints/{id}',
            'jsonKey' => 'endpoint',
            'params' => [
                'id' => $this->idUrlParam,
                'interface' => [
                    'type' => 'string',
                    'description' => Desc::$interface,
                ],
                'name' => [
                    'type' => 'string',
                    'description' => Desc::id('endpoint')
                ],
                'region' => [
                    'type' => 'string',
                    'description' => Desc::$region,
                ],
                'url' => [
                    'type' => 'string',
                    'description' => Desc::$endpointUrl,
                ],
                'serviceId' => [
                    'type' => 'string',
                    'sentAs' => 'service_id',
                    'description' => Desc::id('service') . ' that this endpoint belongs to',
                ],
            ]
        ];
    }

    public function deleteEndpoint()
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
                    'description' => Desc::name('domain')
                ],
                'enabled' => [
                    'type' => 'boolean',
                    'description' => Desc::enabled('domain'),
                ],
                'description' => [
                    'type' => 'string',
                    'description' => Desc::desc('domain'),
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
                    'description' => Desc::name('domain')
                ],
                'enabled' => [
                    'type' => 'boolean',
                    'location' => 'query',
                    'description' => Desc::enabled('domain'),
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
                    'description' => Desc::name('domain'),
                ],
                'enabled' => [
                    'type' => 'boolean',
                    'description' => Desc::enabled('domain'),
                ],
                'description' => [
                    'type' => 'string',
                    'description' => Desc::desc('domain'),
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
                'userId'   => $this->idUrlParam,
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
            'method' => 'HEAD',
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
            'jsonKey' => 'project',
            'params' => [
                'description' => [
                    'type' => 'string',
                    'description' => Desc::enabled('project'),
                ],
                'domainId' => [
                    'type' => 'string',
                    'sentAs' => 'domain_id',
                    'description' => Desc::id('domain') . ' associated with this project',
                ],
                'parentId' => [
                    'type' => 'string',
                    'sentAs' => 'parent_id',
                    'description' => Desc::$projectParent,
                ],
                'enabled' => [
                    'type' => 'boolean',
                    'description' => Desc::enabled('project')
                ],
                'name' => [
                    'type' => 'string',
                    'required' => true,
                    'description' => Desc::name('project'),
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
                    'location' => 'query',
                    'description' => Desc::id('domain') . ' associated with this project',
                ],
                'enabled' => [
                    'type' => 'boolean',
                    'location' => 'query',
                    'description' => Desc::enabled('project')
                ],
                'name' => [
                    'type' => 'string',
                    'location' => 'query',
                    'description' => Desc::name('project')
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
            'jsonKey' => 'project',
            'params' => [
                'id' => $this->idUrlParam,
                'description' => [
                    'type' => 'string',
                    'description' => Desc::desc('project')
                ],
                'domainId' => [
                    'type' => 'string',
                    'sentAs' => 'domain_id',
                    'description' => Desc::id('domain') . ' associated with this project',
                ],
                'parentId' => [
                    'type' => 'string',
                    'sentAs' => 'parent_id',
                    'description' => Desc::$projectParent
                ],
                'enabled' => [
                    'type' => 'boolean',
                    'description' => Desc::enabled('project'),
                ],
                'name' => [
                    'type' => 'string',
                    'description' => Desc::name('project'),
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
                    'type'   => 'string',
                    'description' => Desc::$defaultProject,
                ],
                'description' => [
                    'type' => 'string',
                    'description' => Desc::desc('user'),
                ],
                'domainId' => [
                    'type' => 'string',
                    'sentAs' => 'domain_id',
                    'description' => Desc::id('domain') . ' associated with this user',
                ],
                'email' => [
                    'type' => 'string',
                    'description' => Desc::$email,
                ],
                'enabled' => [
                    'type' => 'boolean',
                    'description' => Desc::enabled('user'),
                ],
                'name' => [
                    'type' => 'string',
                    'required' => true,
                    'description' => Desc::enabled('name'),
                ],
                'password' => [
                    'type' => 'string',
                    'description' => Desc::$password,
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
                    'location' => 'query',
                    'description' => 'Filters by the ' . Desc::id('domain') . ' associated with the users',
                ],
                'enabled' => [
                    'type' => 'boolean',
                    'location' => 'query',
                    'description' => 'Filters by the "enabled" status of the user'
                ],
                'name' => [
                    'type' => 'string',
                    'location' => 'query',
                    'description' => 'Filters by the name of the user',
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
                    'type' => 'string',
                    'description' => Desc::desc('user'),
                ],
                'email' => [
                    'type' => 'string',
                    'description' => Desc::$email,
                ],
                'enabled' => [
                    'type' => 'boolean',
                    'description' => Desc::enabled('user'),
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
                    'description' => Desc::desc('group'),
                ],
                'domainId' => [
                    'type' => 'string',
                    'sentAs' => 'domain_id',
                    'description' => Desc::id('domain') . ' associated with this group',
                ],
                'name' => [
                    'type' => 'string',
                    'required' => true,
                    'description' => Desc::name('group'),
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
                    'description' => Desc::id('domain') . ' associated with the groups',
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
            'jsonKey' => 'group',
            'params' => [
                'id' => $this->idUrlParam,
                'description' => [
                    'type' => 'string',
                    'description' => Desc::desc('group'),
                ],
                'name' => [
                    'type' => 'string',
                    'description' => Desc::name('group'),
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
            'method' => 'HEAD',
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
                    'type' => 'string',
                    'description' => "This does something, but it's not explained in the docs (as of writing this)"
                ],
                'projectId' => [
                    'type' => 'string',
                    'sentAs' => 'project_id',
                    'description' => Desc::id('project') . ' of the project'
                ],
                'type' => [
                    'type' => 'string',
                    'description' => "This does something, but it's not explained in the docs (as of writing this)"
                ],
                'userId' => [
                    'type' => 'string',
                    'sentAs' => 'user_id',
                    'description' => Desc::id('user') . ' of the user'
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
            'params' => ['id' => $this->idUrlParam] + $this->postCredentials()['params']
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
                    'required' => true,
                    'description' => Desc::name('role'),
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
                    'location' => 'query',
                    'description' => Desc::name('role'),
                ]
            ]
        ];
    }

    public function deleteRole()
    {
        return [
            'method' => 'DELETE',
            'path'   => 'roles/{id}',
            'params' => [
                'id' => $this->idUrlParam
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
                    'location' => 'query',
                    'description' => 'Filter by user ID'
                ],
                'groupId' => [
                    'sentAs' => 'group.id',
                    'location' => 'query',
                    'description' => 'Filter by group ID'
                ],
                'roleId' => [
                    'sentAs' => 'role.id',
                    'location' => 'query',
                    'description' => 'Filter by role ID'
                ],
                'domainId' => [
                    'sentAs' => 'scope.domain.id',
                    'location' => 'query',
                    'description' => Desc::id('domain') . ' associated with the role assignments',
                ],
                'projectId' => [
                    'sentAs' => 'scope.project.id',
                    'location' => 'query',
                    'description' => 'Filter by project ID'
                ],
                'effective' => [
                    'type' => 'boolean',
                    'location' => 'query',
                    'description' => Desc::$effective,
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
                    'description' => "This does something, but it's not explained in the docs (as of writing this)"
                ],
                'projectId' => [
                    'type' => 'string',
                    'sentAs' => 'project_id',
                    'description' => Desc::id('project') . ' of the project'
                ],
                'type' => [
                    'type' => 'string',
                    'description' => "This does something, but it's not explained in the docs (as of writing this)"
                ],
                'userId' => [
                    'type' => 'string',
                    'sentAs' => 'user_id',
                    'description' => Desc::id('user') . ' of the user'
                ],
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
                    'location' => 'query',
                    'description' => 'Filter by type'
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
                    'description' => "This does something, but it's not explained in the docs (as of writing this)"
                ],
                'projectId' => [
                    'type' => 'string',
                    'sentAs' => 'project_id',
                    'description' => Desc::id('project') . ' of the project'
                ],
                'type' => [
                    'type' => 'string',
                    'description' => "This does something, but it's not explained in the docs (as of writing this)"
                ],
                'userId' => [
                    'type' => 'string',
                    'sentAs' => 'user_id',
                    'description' => Desc::id('user') . ' of the user'
                ],
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