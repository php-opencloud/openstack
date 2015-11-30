<?php

namespace OpenStack\Common;

class JsonPatch
{
    const OP_ADD = 'add';
    const OP_REPLACE = 'replace';
    const OP_REMOVE = 'remove';

    public function makeDiff($srcStruct, $desStruct, $path = '', $decode = true)
    {
        if ($decode) {
            $srcStruct = json_decode($srcStruct);
            $desStruct = json_decode($desStruct);
        }

        $changes = [];

        if (is_object($srcStruct) && $this->shouldPartiallyReplace($srcStruct, $desStruct)) {
            foreach ($desStruct as $key => $val) {
                if (!property_exists($srcStruct, $key)) {
                    $changes[] = $this->makePatch(self::OP_ADD, $this->path($path, $key), $val);
                } elseif ($srcStruct->$key != $val) {
                    $changes += $this->makeDiff($srcStruct->$key, $val, $this->path($path, $key), false);
                }
            }
        } elseif (is_array($srcStruct) && ($diff = $this->arrayDiff($desStruct, $srcStruct))) {
            foreach ($diff as $key => $val) {
                if (is_object($val)) {
                    $changes += $this->makeDiff($srcStruct[$key], $val, $this->path($path, $key), false);
                } else {
                    $op = array_key_exists($key, $srcStruct) && !in_array($srcStruct[$key], $desStruct, true)
                        ? self::OP_REPLACE : self::OP_ADD;
                    $changes[] = $this->makePatch($op, $this->path($path, $key), $val);
                }
            }
        } elseif (is_object($srcStruct) && $this->shouldPartiallyReplace($desStruct, $srcStruct)) {
            foreach ($srcStruct as $key => $val) {
                if (!property_exists($desStruct, $key)) {
                    $changes[] = $this->makePatch(self::OP_REMOVE, $this->path($path, $key));
                }
            }
        } elseif (is_array($srcStruct) && $srcStruct != $desStruct) {
            foreach ($srcStruct as $key => $val) {
                if (!in_array($val, $desStruct, true)) {
                    $changes[] = $this->makePatch(self::OP_REMOVE, $this->path($path, $key));
                }
            }
        } elseif ($srcStruct != $desStruct) {
            $changes[] = $this->makePatch(self::OP_REPLACE, $path, $desStruct);
        }

        return $decode ? json_encode($changes, JSON_UNESCAPED_SLASHES) : $changes;
    }

    private function shouldPartiallyReplace($o1, $o2)
    {
        return count(array_diff_key((array) $o1, (array) $o2)) < count($o1);
    }

    private function arrayDiff(array $a1, array $a2)
    {
        $result = [];

        foreach($a1 as $key => $val) {
            if (!in_array($val, $a2, true)) {
                $result[$key] = $val;
            }
        }

        return $result;
    }

    private function path($root, $path)
    {
        if ($path === '_empty_') {
            $path = '';
        }

        return rtrim($root, '/') . '/' . ltrim($path, '/');
    }

    private function makePatch($op, $path, $val = null)
    {
        switch ($op) {
            default:
                return ['op' => $op, 'path' => $path, 'value' => $val];
            case self::OP_REMOVE:
                return ['op' => $op, 'path' => $path];
        }

    }
}