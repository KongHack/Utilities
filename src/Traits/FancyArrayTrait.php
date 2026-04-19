<?php
namespace GCWorld\Utilities\Traits;

use Ramsey\Uuid\Uuid;

/**
 * FancyArrayTrait Trait
 *
 * Note: This is setup for Bootstrap 3.
 */
trait FancyArrayTrait
{
    /**
     * @param mixed      $value
     * @param mixed|null $key
     *
     * @return string
     */
    public static function renderFancyArrayValue(mixed $value, mixed $key = null): string
    {
        if (\is_array($value)) {
            if ([] === $value) {
                return '<span class="text-muted">[]</span>';
            }

            $out = '<table class="table table-condensed table-bordered" style="margin-bottom:8px;">';
            $out .= '<tbody>';

            foreach ($value as $k => $v) {
                $out .= '<tr>';
                $out .= '<th class="text-nowrap" style="width:1%; vertical-align:top;">'
                    .\htmlspecialchars((string) $k, ENT_QUOTES, 'UTF-8')
                    .'</th>';
                $out .= '<td style="vertical-align:top;">'
                    .self::renderFancyArrayValue($v, $k)
                    .'</td>';
                $out .= '</tr>';
            }

            $out .= '</tbody></table>';

            return $out;
        }

        if (\is_object($value)) {
            $class = \get_class($value);

            if ($value instanceof \JsonSerializable) {
                return '<div><span class="label label-info">Object</span> '
                    .\htmlspecialchars($class, ENT_QUOTES, 'UTF-8')
                    .'</div>'
                    .self::renderFancyArrayValue($value->jsonSerialize(), $key);
            }

            if (\method_exists($value, 'getArray')) {
                return '<div><span class="label label-info">Object</span> '
                    .\htmlspecialchars($class, ENT_QUOTES, 'UTF-8')
                    .'</div>'
                    .self::renderFancyArrayValue($value->getArray(), $key);
            }

            if (\method_exists($value, '__toString')) {
                return '<div><span class="label label-info">Object</span> '
                    .\htmlspecialchars($class, ENT_QUOTES, 'UTF-8')
                    .'</div><pre style="margin:0;">'
                    .\htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8')
                    .'</pre>';
            }

            return '<span class="text-danger">Object not printable: '
                .\htmlspecialchars($class, ENT_QUOTES, 'UTF-8')
                .'</span>';
        }

        return self::renderFancyJsonScalar($value, $key);
    }

    /**
     * @param mixed      $value
     * @param mixed|null $key
     *
     * @return string
     */
    protected static function renderFancyJsonScalar(mixed $value, mixed $key = null): string
    {
        if (null === $value) {
            return '<em class="text-muted">null</em>';
        }

        if (true === $value) {
            return '<span class="label label-success">true</span>';
        }

        if (false === $value) {
            return '<span class="label label-default">false</span>';
        }

        if (self::looksLikeBinaryUuidField($key, $value)) {
            $uuid = self::tryFormatBinaryUuid($value);
            if (null !== $uuid) {
                return '<code>'.\htmlspecialchars($uuid, ENT_QUOTES, 'UTF-8').'</code>';
            }
        }

        if (\is_string($value)) {
            $trimmed = \trim($value);

            if ('' === $trimmed) {
                return '<em class="text-muted">""</em>';
            }

            if (($trimmed[0] ?? '') === '{' || ($trimmed[0] ?? '') === '[') {
                $decoded = \json_decode($value, true);
                if (JSON_ERROR_NONE === \json_last_error()) {
                    return self::renderFancyArrayValue($decoded, $key);
                }
            }

            return '<code>'.\htmlspecialchars($value, ENT_QUOTES, 'UTF-8').'</code>';
        }

        if (\is_int($value) || \is_float($value)) {
            return '<span class="text-primary">'.\htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8').'</span>';
        }

        return \htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }

    /**
     * @param mixed $key
     * @param mixed $value
     *
     * @return bool
     */
    protected static function looksLikeBinaryUuidField(mixed $key, mixed $value): bool
    {
        if (!\is_string($key) && !\is_int($key)) {
            return false;
        }

        if (!\is_string($value)) {
            return false;
        }

        $key = (string) $key;

        return \str_contains($key, '_uuid') && 16 === \strlen($value);
    }

    /**
     * @param string $value
     *
     * @return string|null
     */
    protected static function tryFormatBinaryUuid(string $value): ?string
    {
        try {
            return 'UUID: '.Uuid::fromBytes($value)->toString();
        } catch (\Throwable) {
            return null;
        }
    }
}