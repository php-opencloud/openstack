<?php

namespace OpenStack\Common\JsonSchema;

class JsonPatch
{
    const OP_ADD     = 'add';
    const OP_REPLACE = 'replace';
    const OP_REMOVE  = 'remove';

    public static function diff($src, $dest)
    {
        return (new static)->makeDiff($src, $dest);
    }

    public function makeDiff($srcStruct, $desStruct, $path = '')
    {
        $changes = [];

        if (is_object($srcStruct)) {
            $changes = $this->handleObject($srcStruct, $desStruct, $path);
        } elseif (is_array($srcStruct)) {
            $changes = $this->handleArray($srcStruct, $desStruct, $path);
        } elseif ($srcStruct != $desStruct) {
            $changes[] = $this->makePatch(self::OP_REPLACE, $path, $desStruct);
        }

        return $changes;
    }

    protected function handleArray($srcStruct, $desStruct, $path)
    {
        $changes = [];

        if ($diff = $this->arrayDiff($desStruct, $srcStruct)) {
            foreach ($diff as $key => $val) {
                if (is_object($val)) {
                    $changes = array_merge($changes, $this->makeDiff($srcStruct[$key], $val, $this->path($path, $key)));
                } else {
                    $op = array_key_exists($key, $srcStruct) && !in_array($srcStruct[$key], $desStruct, true)
                        ? self::OP_REPLACE : self::OP_ADD;
                    $changes[] = $this->makePatch($op, $this->path($path, $key), $val);
                }
            }
        } elseif ($srcStruct != $desStruct) {
            foreach ($srcStruct as $key => $val) {
                if (!in_array($val, $desStruct, true)) {
                    $changes[] = $this->makePatch(self::OP_REMOVE, $this->path($path, $key));
                }
            }
        }

        return $changes;
    }

    protected function handleObject($srcStruct, $desStruct, $path)
    {
        $changes = [];

        if ($this->shouldPartiallyReplace($srcStruct, $desStruct)) {
            foreach ($desStruct as $key => $val) {
                if (!property_exists($srcStruct, $key)) {
                    $changes[] = $this->makePatch(self::OP_ADD, $this->path($path, $key), $val);
                } elseif ($srcStruct->$key != $val) {
                    $changes = array_merge($changes, $this->makeDiff($srcStruct->$key, $val, $this->path($path, $key)));
                }
            }
        } elseif ($this->shouldPartiallyReplace($desStruct, $srcStruct)) {
            foreach ($srcStruct as $key => $val) {
                if (!property_exists($desStruct, $key)) {
                    $changes[] = $this->makePatch(self::OP_REMOVE, $this->path($path, $key));
                }
            }
        }

        return $changes;
    }

    protected function shouldPartiallyReplace($o1, $o2)
    {
        return count(array_diff_key((array) $o1, (array) $o2)) < count($o1);
    }

    protected function arrayDiff(array $a1, array $a2)
    {
        $result = [];

        foreach($a1 as $key => $val) {
            if (!in_array($val, $a2, true)) {
                $result[$key] = $val;
            }
        }

        return $result;
    }

    protected function path($root, $path)
    {
        if ($path === '_empty_') {
            $path = '';
        }

        return rtrim($root, '/') . '/' . ltrim($path, '/');
    }

    protected function makePatch($op, $path, $val = null)
    {
        switch ($op) {
            default:
                return ['op' => $op, 'path' => $path, 'value' => $val];
            case self::OP_REMOVE:
                return ['op' => $op, 'path' => $path];
        }

    }
}