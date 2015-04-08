<?php

namespace OpenStack\Common\Api;

class Parameter
{
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
        $this->name = $data['name'];
        $this->location = isset($data['location']) ? $data['location'] : self::DEFAULT_LOCATION;

        if (isset($data['type'])) {
            $this->type = $data['type'];
        }

        if (isset($data['path'])) {
            $this->path = $data['path'];
        }

        if (isset($data['required'])) {
            $this->required = (bool) $data['required'];
        }

        if (isset($data['sentAs'])) {
            $this->sentAs = $data['sentAs'];
        }

        if (isset($data['items'])) {
            $this->itemSchema = new Parameter($data['items'] + ['name' => $this->name . '[]']);
        }

        if (isset($data['prefix'])) {
            $this->prefix = $data['prefix'];
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
                if (!isset($this->properties[$key])) {
                    if ($this->name == 'metadata') {
                        $property = $this->properties;
                    } else {
                        throw new \Exception(sprintf('The key provided "%s" is not defined', $key));
                    }
                } else {
                    $property = $this->properties[$key];
                }

                $property->validate($userValue);
            }
        }

        return true;
    }

    private function hasCorrectType($userValue)
    {
        // Don't worry about undefined types
        if (!$this->type) {
            return false;
        }

        // Helper fn to see whether an array is associative (i.e. a JSON object)
        $isAssociative = function (array $array) {
            return (bool) count(array_filter(array_keys($array), 'is_string'));
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
