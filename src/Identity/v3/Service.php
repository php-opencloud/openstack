<?php

namespace OpenStack\Identity\v3;

use OpenStack\Common\Auth\IdentityService;
use OpenStack\Common\Error\BadResponseError;
use OpenStack\Common\Service\AbstractService;

/**
 * Represents the Keystone v3 service.
 *
 * @property \OpenStack\Identity\v3\Api $api
 */
class Service extends AbstractService implements IdentityService
{
    /**
     * Authenticates credentials, giving back a token and a base URL for the service.
     *
     * @param  array $options {@see \OpenStack\Identity\v3\Api::postTokens}
     *
     * @return array Returns a {@see Models\Token} as the first element, a string base URL as the second
     */
    public function authenticate(array $options)
    {
        $authOptions = array_intersect_key($options, $this->api->postTokens()['params']);

        $token = $this->generateToken($authOptions);

        $baseUrl = $token->catalog->getServiceUrl(
            $options['catalogName'],
            $options['catalogType'],
            $options['region'],
            isset($options['interface']) ? $options['interface'] : Enum::INTERFACE_PUBLIC
        );

        return [$token, $baseUrl];
    }

    /**
     * Generates a new authentication token
     *
     * @param array $options {@see \OpenStack\Identity\v3\Api::postTokens}
     *
     * @return Models\Token
     */
    public function generateToken(array $options)
    {
        return $this->model('Token')->create($options);
    }

    /**
     * Retrieves a token object and populates its unique identifier object. This operation will not perform a GET or
     * HEAD request by default; you will need to call retrieve() if you want to pull in remote state from the API.
     *
     * @param string $id The unique ID of the token to retrieve
     *
     * @return Models\Token
     */
    public function getToken($id)
    {
        return $this->model('Token', ['id' => $id]);
    }

    /**
     * Validates a token, identified by its ID, and returns TRUE if its valid, FALSE if not.
     *
     * @param string $id The unique ID of the token
     *
     * @return bool
     */
    public function validateToken($id)
    {
        try {
            $this->execute($this->api->headTokens(), ['tokenId' => $id]);
            return true;
        } catch (BadResponseError $e) {
            return false;
        }
    }

    /**
     * Revokes a token, identified by its ID. After this operation completes, users will not be able to use this token
     * again for authentication.
     *
     * @param string $id The unique ID of the token
     *
     * @return Models\Token
     */
    public function revokeToken($id)
    {
        $this->execute($this->api->deleteTokens(), ['tokenId' => $id]);
    }

    /**
     * Creates a new service according to the provided options.
     *
     * @param array $options {@see \OpenStack\Identity\v3\Api::postServices}
     *
     * @return Models\Service
     */
    public function createService(array $options)
    {
        return $this->model('Service')->create($options);
    }

    /**
     * Returns a generator which will yield a collection of service objects. The elements which generators yield can be
     * accessed using a foreach loop. Often the API will not return the full state of the resource in collections; you
     * will need to use retrieve() to pull in the full state of the remote resource from the API.
     *
     * @param array $options {@see \OpenStack\Identity\v3\Api::getServices}
     *
     * @return \Generator
     */
    public function listServices(array $options = [])
    {
        $operation = $this->getOperation($this->api->getServices(), $options);

        return $this->model('Service')->enumerate($operation);
    }

    /**
     * Retrieves a service object and populates its unique identifier object. This operation will not perform a GET or
     * HEAD request by default; you will need to call retrieve() if you want to pull in remote state from the API.
     *
     * @param string $id The unique ID of the service
     *
     * @return Models\Service
     */
    public function getService($id)
    {
        return $this->model('Service', ['id' => $id]);
    }

    /**
     * Creates a new endpoint according to the provided options.
     *
     * @param array $options {@see \OpenStack\Identity\v3\Api::postEndpoints}
     *
     * @return Models\Endpoint
     */
    public function createEndpoint(array $options)
    {
        return $this->model('Endpoint')->create($options);
    }

    /**
     * Retrieves an endpoint object and populates its unique identifier object. This operation will not perform a GET or
     * HEAD request by default; you will need to call retrieve() if you want to pull in remote state from the API.
     *
     * @param string $id The unique ID of the service
     *
     * @return Models\Endpoint
     */
    public function getEndpoint($id)
    {
        return $this->model('Endpoint', ['id' => $id]);
    }

    /**
     * Returns a generator which will yield a collection of endpoint objects. The elements which generators yield can be
     * accessed using a foreach loop. Often the API will not return the full state of the resource in collections; you
     * will need to use retrieve() to pull in the full state of the remote resource from the API.
     *
     * @param array $options {@see \OpenStack\Identity\v3\Api::getEndpoints}
     *
     * @return \Generator
     */
    public function listEndpoints(array $options = [])
    {
        $operation = $this->getOperation($this->api->getEndpoints(), $options);

        return $this->model('Endpoint')->enumerate($operation);
    }

    /**
     * Creates a new domain according to the provided options.
     *
     * @param array $options {@see \OpenStack\Identity\v3\Api::postDomains}
     *
     * @return Models\Domain
     */
    public function createDomain(array $options)
    {
        return $this->model('Domain')->create($options);
    }

    /**
     * Returns a generator which will yield a collection of domain objects. The elements which generators yield can be
     * accessed using a foreach loop. Often the API will not return the full state of the resource in collections; you
     * will need to use retrieve() to pull in the full state of the remote resource from the API.
     *
     * @param array $options {@see \OpenStack\Identity\v3\Api::getDomains}
     *
     * @return \Generator
     */
    public function listDomains(array $options = [])
    {
        $operation = $this->getOperation($this->api->getDomains(), $options);

        return $this->model('Domain')->enumerate($operation);
    }

    /**
     * Retrieves a domain object and populates its unique identifier object. This operation will not perform a GET or
     * HEAD request by default; you will need to call retrieve() if you want to pull in remote state from the API.
     *
     * @param string $id The unique ID of the domain
     *
     * @return Models\Domain
     */
    public function getDomain($id)
    {
        return $this->model('Domain', ['id' => $id]);
    }

    /**
     * Creates a new project according to the provided options.
     *
     * @param array $options {@see \OpenStack\Identity\v3\Api::postProjects}
     *
     * @return Models\Project
     */
    public function createProject(array $options)
    {
        return $this->model('Project')->create($options);
    }

    /**
     * Returns a generator which will yield a collection of project objects. The elements which generators yield can be
     * accessed using a foreach loop. Often the API will not return the full state of the resource in collections; you
     * will need to use retrieve() to pull in the full state of the remote resource from the API.
     *
     * @param array $options {@see \OpenStack\Identity\v3\Api::getProjects}
     *
     * @return \Generator
     */
    public function listProjects(array $options = [])
    {
        $operation = $this->getOperation($this->api->getProjects(), $options);

        return $this->model('Project')->enumerate($operation);
    }

    /**
     * Retrieves a project object and populates its unique identifier object. This operation will not perform a GET or
     * HEAD request by default; you will need to call retrieve() if you want to pull in remote state from the API.
     *
     * @param string $id The unique ID of the project
     *
     * @return Models\Project
     */
    public function getProject($id)
    {
        return $this->model('Project', ['id' => $id]);
    }

    /**
     * Creates a new user according to the provided options.
     *
     * @param array $options {@see \OpenStack\Identity\v3\Api::postUsers}
     *
     * @return Models\User
     */
    public function createUser(array $options)
    {
        return $this->model('User')->create($options);
    }

    /**
     * Returns a generator which will yield a collection of user objects. The elements which generators yield can be
     * accessed using a foreach loop. Often the API will not return the full state of the resource in collections; you
     * will need to use retrieve() to pull in the full state of the remote resource from the API.
     *
     * @param array $options {@see \OpenStack\Identity\v3\Api::getUsers}
     *
     * @return \Generator
     */
    public function listUsers(array $options = [])
    {
        $operation = $this->getOperation($this->api->getUsers(), $options);

        return $this->model('User')->enumerate($operation);
    }

    /**
     * Retrieves a user object and populates its unique identifier object. This operation will not perform a GET or
     * HEAD request by default; you will need to call retrieve() if you want to pull in remote state from the API.
     *
     * @param string $id The unique ID of the user
     *
     * @return Models\User
     */
    public function getUser($id)
    {
        return $this->model('User', ['id' => $id]);
    }

    /**
     * Creates a new group according to the provided options.
     *
     * @param array $options {@see \OpenStack\Identity\v3\Api::postGroups}
     *
     * @return Models\Group
     */
    public function createGroup(array $options)
    {
        return $this->model('Group')->create($options);
    }

    /**
     * Returns a generator which will yield a collection of group objects. The elements which generators yield can be
     * accessed using a foreach loop. Often the API will not return the full state of the resource in collections; you
     * will need to use retrieve() to pull in the full state of the remote resource from the API.
     *
     * @param array $options {@see \OpenStack\Identity\v3\Api::getGroups}
     *
     * @return \Generator
     */
    public function listGroups(array $options = [])
    {
        $operation = $this->getOperation($this->api->getGroups(), $options);

        return $this->model('Group')->enumerate($operation);
    }

    /**
     * Retrieves a group object and populates its unique identifier object. This operation will not perform a GET or
     * HEAD request by default; you will need to call retrieve() if you want to pull in remote state from the API.
     *
     * @param string $id The unique ID of the group
     *
     * @return Models\Group
     */
    public function getGroup($id)
    {
        return $this->model('Group', ['id' => $id]);
    }

    /**
     * Creates a new credential according to the provided options.
     *
     * @param array $options {@see \OpenStack\Identity\v3\Api::postCredentials}
     *
     * @return Models\Credential
     */
    public function createCredential(array $options)
    {
        return $this->model('Credential')->create($options);
    }

    /**
     * Returns a generator which will yield a collection of credential objects. The elements which generators yield can
     * be accessed using a foreach loop. Often the API will not return the full state of the resource in collections;
     * you will need to use retrieve() to pull in the full state of the remote resource from the API.
     *
     * @return \Generator
     */
    public function listCredentials()
    {
        $operation = $this->getOperation($this->api->getCredentials());

        return $this->model('Credential')->enumerate($operation);
    }

    /**
     * Retrieves a credential object and populates its unique identifier object. This operation will not perform a GET
     * or HEAD request by default; you will need to call retrieve() if you want to pull in remote state from the API.
     *
     * @param string $id The unique ID of the credential
     *
     * @return Models\Credential
     */
    public function getCredential($id)
    {
        return $this->model('Credential', ['id' => $id]);
    }

    /**
     * Creates a new role according to the provided options.
     *
     * @param array $options {@see \OpenStack\Identity\v3\Api::postRoles}
     *
     * @return Models\Role
     */
    public function createRole(array $options)
    {
        return $this->model('Role')->create($options);
    }

    /**
     * Returns a generator which will yield a collection of role objects. The elements which generators yield can be
     * accessed using a foreach loop. Often the API will not return the full state of the resource in collections; you
     * will need to use retrieve() to pull in the full state of the remote resource from the API.
     *
     * @param array $options {@see \OpenStack\Identity\v3\Api::getRoles}
     *
     * @return \Generator
     */
    public function listRoles(array $options = [])
    {
        $operation = $this->getOperation($this->api->getRoles(), $options);

        return $this->model('Role')->enumerate($operation);
    }

    /**
     * Returns a generator which will yield a collection of role assignment objects. The elements which generators
     * yield can be accessed using a foreach loop. Often the API will not return the full state of the resource in
     * collections; you will need to use retrieve() to pull in the full state of the remote resource from the API.
     *
     * @param array $options {@see \OpenStack\Identity\v3\Api::getRoleAssignments}
     *
     * @return \Generator
     */
    public function listRoleAssignments(array $options = [])
    {
        $operation = $this->getOperation($this->api->getRoleAssignments(), $options);

        return $this->model('Assignment')->enumerate($operation);
    }

    /**
     * Creates a new policy according to the provided options.
     *
     * @param array $options {@see \OpenStack\Identity\v3\Api::postPolicies}
     *
     * @return Models\Policy
     */
    public function createPolicy(array $options)
    {
        return $this->model('Policy')->create($options);
    }

    /**
     * Returns a generator which will yield a collection of policy objects. The elements which generators yield can be
     * accessed using a foreach loop. Often the API will not return the full state of the resource in collections; you
     * will need to use retrieve() to pull in the full state of the remote resource from the API.
     *
     * @param array $options {@see \OpenStack\Identity\v3\Api::getPolicies}
     *
     * @return \Generator
     */
    public function listPolicies(array $options = [])
    {
        $operation = $this->getOperation($this->api->getPolicies(), $options);

        return $this->model('Policy')->enumerate($operation);
    }

    /**
     * Retrieves a policy object and populates its unique identifier object. This operation will not perform a GET or
     * HEAD request by default; you will need to call retrieve() if you want to pull in remote state from the API.
     *
     * @param string $id The unique ID of the policy
     *
     * @return Models\Policy
     */
    public function getPolicy($id)
    {
        return $this->model('Policy', ['id' => $id]);
    }
}