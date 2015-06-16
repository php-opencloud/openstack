<?php

namespace OpenStack\Identity\v3;

use OpenStack\Common\Service\AbstractService;
use OpenStack\Common\Service\Builder;

/**
 * @property \OpenStack\Identity\v3\Api $api
 */
class Service extends AbstractService
{
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
     * @param string $id
     *
     * @return Models\Token
     */
    public function getToken($id)
    {
        $token = $this->model('Token', ['id' => $id]);
        $token->retrieve();

        return $token;
    }

    /**
     * @param string $id
     *
     * @return bool
     */
    public function validateToken($id)
    {
        $response = $this->execute($this->api->headTokens(), ['tokenId' => $id]);
        return $response->getStatusCode() === 204;
    }

    /**
     * @param string $id
     */
    public function revokeToken($id)
    {
        $this->execute($this->api->deleteTokens(), ['tokenId' => $id]);
    }

    public function createService(array $data)
    {
        return $this->model('Service')->create($data);
    }

    public function listServices()
    {
        $operation = $this->getOperation($this->api->getServices());

        return $this->model('Service')->enumerate($operation);
    }

    public function getService($id)
    {
        return $this->model('Service', ['id' => $id]);
    }

    public function createEndpoint(array $data)
    {
        return $this->model('Endpoint')->create($data);
    }

    public function createDomain(array $data)
    {
        return $this->model('Domain')->create($data);
    }

    public function listDomains()
    {

    }

    public function getDomain()
    {

    }

    public function createProject()
    {

    }

    public function listProjects()
    {

    }

    public function getProject()
    {

    }

    public function createUser()
    {

    }

    public function listUsers()
    {

    }

    public function getUser()
    {

    }

    public function createGroup()
    {

    }

    public function listGroups()
    {

    }

    public function getGroup()
    {

    }

    public function createCredential()
    {

    }

    public function listCredentials()
    {

    }

    public function getCredential()
    {

    }

    public function createRole()
    {

    }

    public function listRoles()
    {

    }

    public function listRoleAssignments()
    {

    }

    public function createPolicy()
    {

    }

    public function listPolicies()
    {

    }

    public function getPolicy()
    {

    }
}