<?php

namespace OpenStack\Common\Api;

use OpenStack\Common\HydratorStrategyTrait;

class Parameter
{
    use HydratorStrategyTrait;

    const DEFAULT_LOCATION = 'json';

    private $name;
    private $sentAs;
    private $itemSchema;
    private $properties;
    private $type;
    private $required;
    private $location;
    private $path;
    private $prefix;

    public function __construct(array $data)
    {
        $this->hydrate($data);

        $this->location = $this->location ?: self::DEFAULT_LOCATION;
        $this->required = (bool) $this->required;

        if (isset($data['items'])) {
            $this->itemSchema = new Parameter($data['items']);
        }

        if (isset($data['properties'])) {
            if ($this->name == 'metadata') {
                $this->properties = new Parameter($data['properties']);
            } else {
                foreach ($data['properties'] as $name => $property) {
                    $this->properties[$name] = new Parameter($property + ['name' => $name]);
                }
            }
        }
    }

    public function getName()
    {
        return $this->sentAs ?: $this->name;
    }

    public function isRequired()
    {
        return $this->required === true;
    }

    public function validate($userValues)
    {
        // Check inputted type
        if (!$this->hasCorrectType($userValues)) {
            throw new \Exception(sprintf(
                'The key provided "%s" has the wrong value type. You provided %s but was expecting %s',
                $this->name, print_r($userValues, true), $this->type
            ));
        }

        if ($this->isArray()) {
            foreach ($userValues as $userValue) {
                $this->itemSchema->validate($userValue);
            }
        } elseif ($this->isObject()) {
            foreach ($userValues as $key => $userValue) {
                // Check that nested keys are properly defined, but permit arbitrary structures if it's metadata
                $property = $this->getNestedProperty($key);
                $property->validate($userValue);
            }
        }

        return true;
    }

    private function getNestedProperty($key)
    {
        if ($this->name == 'metadata' && $this->properties instanceof Parameter) {
            return $this->properties;
        } elseif (isset($this->properties[$key])) {
            return $this->properties[$key];
        } else {
            throw new \Exception(sprintf('The key provided "%s" is not defined', $key));
        }
    }

    private function hasCorrectType($userValue)
    {
        // Helper fn to see whether an array is associative (i.e. a JSON object)
        $isAssociative = function ($value) {
            return is_array($value) && (bool) count(array_filter(array_keys($value), 'is_string'));
        };

        // For params defined as objects, we'll let the user get away with
        // passing in an associative array - since it's effectively a hash
        if ($this->type == 'object' && $isAssociative($userValue)) {
            return true;
        }

        return gettype($userValue) == $this->type;
    }

    public function isArray()
    {
        return $this->type == 'array' && $this->itemSchema instanceof Parameter;
    }

    public function isObject()
    {
        return $this->type == 'object' && !empty($this->properties);
    }

    public function hasLocation($value)
    {
        return $this->location == $value;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getItemSchema()
    {
        return $this->itemSchema;
    }

    public function getProperty($name)
    {
        return isset($this->properties[$name]) ? $this->properties[$name] : null;
    }

    public function getPrefix()
    {
        return $this->prefix;
    }
}
