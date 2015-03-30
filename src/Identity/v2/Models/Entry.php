<?php

namespace OpenStack\Identity\v2\Models;

class Entry
{
    private $name;
    private $type;
    private $endpoints = [];

    public function __construct(array $data)
    {
        $this->name = $data['name'];
        $this->type = $data['type'];

        foreach ($data['endpoints'] as $endpoint) {
            $this->endpoints[] = new Endpoint($endpoint);
        }
    }

    public function matches($name, $type)
    {
        return $this->name == $name && $this->type == $type;
    }

    public function getEndpointUrl($region, $urlType)
    {
        foreach ($this->endpoints as $endpoint) {
            if ($endpoint->supportsRegion($region) && $endpoint->supportsUrlType($urlType)) {
                return $endpoint->getUrl($urlType);
            }
        }

        return null;
    }
}