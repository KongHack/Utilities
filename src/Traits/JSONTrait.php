<?php

namespace GCWorld\Utilities\Traits;

use Exception;
use JsonSerializable;
use stdClass;

/**
 * Trait JSONTrait
 */
trait JSONTrait
{
    /**
     * @param mixed $json
     *
     * @return array<array-key, mixed>
     */
    public static function safe_json_decode(mixed $json): array
    {
        if (empty($json)) {
            return [];
        }

        if (\is_array($json)) {
            return $json;
        }

        if (!\is_string($json)) {
            return [];
        }

        $arr = \json_decode($json, true);

        if (\is_array($arr)) {
            return $arr;
        }

        return [];
    }

    /**
     * @param array<array-key, mixed>|stdClass|JsonSerializable $data
     * @param int                                                $flags
     *
     * @throws Exception
     *
     * @return string
     */
    public static function json_encode(array|JsonSerializable|stdClass $data, int $flags = 0): string
    {
        $result = \json_encode($data, $flags);
        if (false === $result) {
            throw new Exception('JSON Encode Failed');
        }

        return $result;
    }
}
