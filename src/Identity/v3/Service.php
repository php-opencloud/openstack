<?php

namespace OpenStack\Identity\v3;

use OpenStack\Common\Auth\IdentityService;
use OpenStack\Common\Error\BadResponseError;
use OpenStack\Common\Service\AbstractService;

/**
 * @property \OpenStack\Identity\v3\Api $api
 */
class Service extends AbstractService implements IdentityService
{
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
     * Retrieve a token by its unique ID.
     *
     * @param string $id
     *
     * @return Models\Token
     */
    public function getToken($id)
    {
        return $this->model('Token', ['id' => $id]);
    }

    /**
     * @param string $id
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
     * @param string $id
     *
     * @return Models\Token
     */
    public function revokeToken($id)
    {
        $this->execute($this->api->deleteTokens(), ['tokenId' => $id]);
    }

    /**
     * @param array $options {@see \OpenStack\Identity\v3\Api::postServices}
     *
     * @return Models\Service
     */
    public function createService(array $options)
    {
        return $this->model('Service')->create($options);
    }

    /**
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
     * @param string $id
     *
     * @return Models\Service
     */
    public function getService($id)
    {
        return $this->model('Service', ['id' => $id]);
    }

    /**
     * @param array $options {@see \OpenStack\Identity\v3\Api::postEndpoints}
     *
     * @return Models\Endpoint
     */
    public function createEndpoint(array $options)
    {
        return $this->model('Endpoint')->create($options);
    }

    /**
     * @param string $id
     *
     * @return Models\Endpoint
     */
    public function getEndpoint($id)
    {
        return $this->model('Endpoint', ['id' => $id]);
    }

    /**
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
     * @param array $options {@see \OpenStack\Identity\v3\Api::postDomains}
     *
     * @return Models\Domain
     */
    public function createDomain(array $options)
    {
        return $this->model('Domain')->create($options);
    }

    /**
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
     * @param string $id
     *
     * @return Models\Domain
     */
    public function getDomain($id)
    {
        return $this->model('Domain', ['id' => $id]);
    }

    /**
     * @param array $options {@see \OpenStack\Identity\v3\Api::postProjects}
     *
     * @return Models\Project
     */
    public function createProject(array $options)
    {
        return $this->model('Project')->create($options);
    }

    /**
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
     * @param string $id
     *
     * @return Models\Project
     */
    public function getProject($id)
    {
        return $this->model('Project', ['id' => $id]);
    }

    /**
     * @param array $options {@see \OpenStack\Identity\v3\Api::postUsers}
     *
     * @return Models\User
     */
    public function createUser(array $options)
    {
        return $this->model('User')->create($options);
    }

    /**
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
     * @param string $id
     *
     * @return Models\User
     */
    public function getUser($id)
    {
        return $this->model('User', ['id' => $id]);
    }

    /**
     * @param array $options {@see \OpenStack\Identity\v3\Api::postGroups}
     *
     * @return Models\Group
     */
    public function createGroup(array $options)
    {
        return $this->model('Group')->create($options);
    }

    /**
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
     * @param string $id
     *
     * @return Models\Group
     */
    public function getGroup($id)
    {
        return $this->model('Group', ['id' => $id]);
    }

    /**
     * @param array $options {@see \OpenStack\Identity\v3\Api::postCredentials}
     *
     * @return Models\Credential
     */
    public function createCredential(array $options)
    {
        return $this->model('Credential')->create($options);
    }

    /**
     * @return \Generator
     */
    public function listCredentials()
    {
        $operation = $this->getOperation($this->api->getCredentials());

        return $this->model('Credential')->enumerate($operation);
    }

    /**
     * @param string $id
     *
     * @return Models\Credential
     */
    public function getCredential($id)
    {
        return $this->model('Credential', ['id' => $id]);
    }

    /**
     * @param array $options {@see \OpenStack\Identity\v3\Api::postRoles}
     *
     * @return Models\Role
     */
    public function createRole(array $options)
    {
        return $this->model('Role')->create($options);
    }

    /**
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
     * @param array $options {@see \OpenStack\Identity\v3\Api::postPolicies}
     *
     * @return Models\Policy
     */
    public function createPolicy(array $options)
    {
        return $this->model('Policy')->create($options);
    }

    /**
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
     * @param string $id
     *
     * @return Models\Policy
     */
    public function getPolicy($id)
    {
        return $this->model('Policy', ['id' => $id]);
    }
}