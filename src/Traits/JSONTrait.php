<?php
namespace GCWorld\Utilities\Traits;

use stdClass;
use JsonSerializable;
use Exception;

/**
 * Trait JSONTrait
 */
trait JSONTrait
{

    /**
     * @param mixed $json
     *
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     *
     * @return array
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
     * @param array|stdClass|JsonSerializable $data
     * @param int                             $flags
     *
     * @throws Exception
     *
     * @return string
     */
    public static function json_encode(array|JsonSerializable|stdClass $data, int $flags = 0): string
    {
        $result = \json_encode($data, $flags);
        if (!$result) {
            throw new Exception('JSON Encode Failed');
        }

        return $result;
    }
}