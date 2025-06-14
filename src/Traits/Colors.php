<?php
namespace GCWorld\Utilities\Traits;

/**
 * Trait Colors
 */
trait Colors
{
    /**
     * @param string $htmlCode
     * @return float|int
     */
    public static function HTMLToRGB(string $htmlCode): float|int
    {
        if ('#' == $htmlCode[0]) {
            $htmlCode = substr($htmlCode, 1);
        }

        if (3 == strlen($htmlCode)) {
            $htmlCode = $htmlCode[0].$htmlCode[0].$htmlCode[1].$htmlCode[1].$htmlCode[2].$htmlCode[2];
        }

        $r = hexdec($htmlCode[0].$htmlCode[1]);
        $g = hexdec($htmlCode[2].$htmlCode[3]);
        $b = hexdec($htmlCode[4].$htmlCode[5]);

        return $b + ($g << 0x8) + ($r << 0x10);
    }

    /**
     * @param mixed $RGB
     *
     * @return object
     */
    public static function RGBToHSL(mixed $RGB): object
    {
        $r = 0xFF & ($RGB >> 0x10);
        $g = 0xFF & ($RGB >> 0x8);
        $b = 0xFF & $RGB;

        $r = ((float) $r) / 255.0;
        $g = ((float) $g) / 255.0;
        $b = ((float) $b) / 255.0;

        $maxC = max($r, $g, $b);
        $minC = min($r, $g, $b);

        $l = ($maxC + $minC) / 2.0;

        $h = 0;

        if ($maxC == $minC) {
            $s = 0;
            $h = 0;
        } else {
            if ($l < .5) {
                $s = ($maxC - $minC) / ($maxC + $minC);
            } else {
                $s = ($maxC - $minC) / (2.0 - $maxC - $minC);
            }
            if ($r == $maxC) {
                $h = ($g - $b) / ($maxC - $minC);
            }
            if ($g == $maxC) {
                $h = 2.0 + ($b - $r) / ($maxC - $minC);
            }
            if ($b == $maxC) {
                $h = 4.0 + ($r - $g) / ($maxC - $minC);
            }

            $h = $h / 6.0;
        }

        $h = (int) round(255.0 * $h);
        $s = (int) round(255.0 * $s);
        $l = (int) round(255.0 * $l);

        return (object) [
            'hue'        => $h,
            'saturation' => $s,
            'lightness'  => $l,
        ];
    }
}