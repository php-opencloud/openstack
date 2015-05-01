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

    public function postTokens()
    {
        return [
            'method' => 'POST',
            'path'   => 'tokens',
            'params' => [
                'methods' => [
                    'type' => 'array',
                    'path' => 'auth.identity',
                    'items' => [
                        'type' => 'string'
                    ]
                ],
                'user' => [
                    'type'   => 'object',
                    'properties' => [
                        'id'       => [
                            'type' => 'string',
                            'path' => 'auth.identity.password',
                        ],
                        'name'     => [
                            'type' => 'string',
                            'path' => 'auth.identity.password',
                        ],
                        'password' => [
                            'type' => 'string',
                            'path' => 'auth.identity.password',
                        ],
                        'domain'   => $this->domainParam() + ['path' => 'auth.identity.password']
                    ]
                ],
                'tokenId' => [
                    'type'   => 'string',
                    'path'   => 'auth.identity.token',
                    'sentAs' => 'id',
                ],
                'scope' => [
                    'type' => 'object',
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
            'path'   => '',
            'params' => []
        ];
    }

    public function headTokens()
    {
        return [
            'method' => 'HEAD',
            'path'   => '',
            'params' => []
        ];
    }

    public function deleteTokens()
    {
        return [
            'method' => 'DELETE',
            'path'   => '',
            'params' => []
        ];
    }

    public function postServices()
    {
        return [
            'method' => 'POST',
            'path'   => '',
            'params' => []
        ];
    }

    public function getServices()
    {
        return [
            'method' => 'GET',
            'path'   => '',
            'params' => []
        ];
    }

    public function getService()
    {
        return [
            'method' => 'GET',
            'path'   => '',
            'params' => []
        ];
    }

    public function patchService()
    {
        return [
            'method' => 'PATCH',
            'path'   => '',
            'params' => []
        ];
    }

    public function deleteService()
    {
        return [
            'method' => 'DELETE',
            'path'   => '',
            'params' => []
        ];
    }

    public function postEndpoints()
    {
        return [
            'method' => 'POST',
            'path'   => '',
            'params' => []
        ];
    }

    public function getEndpoints()
    {
        return [
            'method' => 'GET',
            'path'   => '',
            'params' => []
        ];
    }

    public function patchEndpoints()
    {
        return [
            'method' => 'PATCH',
            'path'   => '',
            'params' => []
        ];
    }

    public function deleteEndpoints()
    {
        return [
            'method' => 'DELETE',
            'path'   => '',
            'params' => []
        ];
    }

    public function postDomains()
    {
        return [
            'method' => 'POST',
            'path'   => '',
            'params' => []
        ];
    }

    public function getDomains()
    {
        return [
            'method' => 'GET',
            'path'   => '',
            'params' => []
        ];
    }

    public function getDomain()
    {
        return [
            'method' => 'GET',
            'path'   => '',
            'params' => []
        ];
    }

    public function patchDomain()
    {
        return [
            'method' => 'PATCH',
            'path'   => '',
            'params' => []
        ];
    }

    public function deleteDomain()
    {
        return [
            'method' => 'DELETE',
            'path'   => '',
            'params' => []
        ];
    }

    public function getUserRoles()
    {
        return [
            'method' => 'GET',
            'path'   => '',
            'params' => []
        ];
    }

    public function putUserRoles()
    {
        return [
            'method' => 'PUT',
            'path'   => '',
            'params' => []
        ];
    }

    public function headUserRole()
    {
        return [
            'method' => 'HEAD',
            'path'   => '',
            'params' => []
        ];
    }

    public function deleteUserRole()
    {
        return [
            'method' => 'DELETE',
            'path'   => '',
            'params' => []
        ];
    }

    public function getGroupRoles()
    {
        return [
            'method' => 'GET',
            'path'   => '',
            'params' => []
        ];
    }

    public function putGroupRole()
    {
        return [
            'method' => 'PUT',
            'path'   => '',
            'params' => []
        ];
    }

    public function headGroupRole()
    {
        return [
            'method' => 'POST',
            'path'   => '',
            'params' => []
        ];
    }

    public function deleteGroupRole()
    {
        return [
            'method' => 'DELETE',
            'path'   => '',
            'params' => []
        ];
    }

    public function postProjects()
    {
        return [
            'method' => 'POST',
            'path'   => '',
            'params' => []
        ];
    }

    public function getProjects()
    {
        return [
            'method' => 'GET',
            'path'   => '',
            'params' => []
        ];
    }

    public function getProject()
    {
        return [
            'method' => 'GET',
            'path'   => '',
            'params' => []
        ];
    }

    public function patchProject()
    {
        return [
            'method' => 'PATCH',
            'path'   => '',
            'params' => []
        ];
    }

    public function deleteProject()
    {
        return [
            'method' => 'DELETE',
            'path'   => '',
            'params' => []
        ];
    }

    public function getProjectUserRoles()
    {
        return [
            'method' => 'GET',
            'path'   => '',
            'params' => []
        ];
    }

    public function putProjectUserRole()
    {
        return [
            'method' => 'PUT',
            'path'   => '',
            'params' => []
        ];
    }

    public function headProjectUserRole()
    {
        return [
            'method' => 'HEAD',
            'path'   => '',
            'params' => []
        ];
    }

    public function deleteProjectUserRole()
    {
        return [
            'method' => 'DELETE',
            'path'   => '',
            'params' => []
        ];
    }

    public function getProjectGroupRoles()
    {
        return [
            'method' => 'GET',
            'path'   => '',
            'params' => []
        ];
    }

    public function putProjectGroupRole()
    {
        return [
            'method' => 'PUT',
            'path'   => '',
            'params' => []
        ];
    }

    public function headProjectGroupRole()
    {
        return [
            'method' => 'HEAD',
            'path'   => '',
            'params' => []
        ];
    }

    public function deleteProjectGroupRole()
    {
        return [
            'method' => 'DELETE',
            'path'   => '',
            'params' => []
        ];
    }

    public function postUsers()
    {
        return [
            'method' => 'POST',
            'path'   => '',
            'params' => []
        ];
    }

    public function getUsers()
    {
        return [
            'method' => 'GET',
            'path'   => '',
            'params' => []
        ];
    }

    public function getUser()
    {
        return [
            'method' => 'GET',
            'path'   => '',
            'params' => []
        ];
    }

    public function patchUser()
    {
        return [
            'method' => 'PATCH',
            'path'   => '',
            'params' => []
        ];
    }

    public function deleteUser()
    {
        return [
            'method' => 'DELETE',
            'path'   => '',
            'params' => []
        ];
    }

    public function getUserGroups()
    {
        return [
            'method' => 'GET',
            'path'   => '',
            'params' => []
        ];
    }

    public function getUserProjects()
    {
        return [
            'method' => 'GET',
            'path'   => '',
            'params' => []
        ];
    }

    public function postGroups()
    {
        return [
            'method' => 'POST',
            'path'   => '',
            'params' => []
        ];
    }

    public function getGroups()
    {
        return [
            'method' => 'GET',
            'path'   => '',
            'params' => []
        ];
    }

    public function getGroup()
    {
        return [
            'method' => 'GET',
            'path'   => '',
            'params' => []
        ];
    }

    public function patchGroup()
    {
        return [
            'method' => 'PATCH',
            'path'   => '',
            'params' => []
        ];
    }

    public function deleteGroup()
    {
        return [
            'method' => 'DELETE',
            'path'   => '',
            'params' => []
        ];
    }

    public function getGroupUsers()
    {
        return [
            'method' => 'GET',
            'path'   => '',
            'params' => []
        ];
    }

    public function putGroupUser()
    {
        return [
            'method' => 'PUT',
            'path'   => '',
            'params' => []
        ];
    }

    public function deleteGroupUser()
    {
        return [
            'method' => 'DELETE',
            'path'   => '',
            'params' => []
        ];
    }

    public function headGroupUser()
    {
        return [
            'method' => 'GET',
            'path'   => '',
            'params' => []
        ];
    }

    public function postCredentials()
    {
        return [
            'method' => 'POST',
            'path'   => '',
            'params' => []
        ];
    }

    public function getCredentials()
    {
        return [
            'method' => 'GET',
            'path'   => '',
            'params' => []
        ];
    }

    public function getCredential()
    {
        return [
            'method' => 'GET',
            'path'   => '',
            'params' => []
        ];
    }

    public function patchCredential()
    {
        return [
            'method' => 'PATCH',
            'path'   => '',
            'params' => []
        ];
    }

    public function deleteCredential()
    {
        return [
            'method' => 'DELETE',
            'path'   => '',
            'params' => []
        ];
    }

    public function postRoles()
    {
        return [
            'method' => 'POST',
            'path'   => '',
            'params' => []
        ];
    }

    public function getRoles()
    {
        return [
            'method' => 'GET',
            'path'   => '',
            'params' => []
        ];
    }

    public function getRoleAssignments()
    {
        return [
            'method' => 'GET',
            'path'   => '',
            'params' => []
        ];
    }

    public function postPolicies()
    {
        return [
            'method' => 'POST',
            'path'   => '',
            'params' => []
        ];
    }

    public function getPolicies()
    {
        return [
            'method' => 'GET',
            'path'   => '',
            'params' => []
        ];
    }

    public function getPolicy()
    {
        return [
            'method' => 'GET',
            'path'   => '',
            'params' => []
        ];
    }

    public function patchPolicy()
    {
        return [
            'method' => 'PATCH',
            'path'   => '',
            'params' => []
        ];
    }

    public function deletePolicy()
    {
        return [
            'method' => 'DELETE',
            'path'   => '',
            'params' => []
        ];
    }
}