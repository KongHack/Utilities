<?php
namespace GCWorld\Utilities\Traits;

/**
 * Trait Image
 */
trait Image
{
    /**
     * @param string $file_path
     * @param string $new_file_path
     * @param int    $size
     * @param bool   $debug
     * @return bool
     */
    public static function generateThumb(
        string $file_path,
        string $new_file_path,
        int $size = 128,
        bool $debug = false
    ): bool
    {
        $options                = [];
        $options['max_width']   = $size;
        $options['max_height']  = $size;
        [$img_width, $img_height] = @getimagesize($file_path);
        if (!$img_width || !$img_height) {
            return false;
        }
        $scale = min(
            $options['max_width'] / $img_width,
            $options['max_height'] / $img_height
        );
        if ($scale > 1) {
            $scale = 1;
        }
        $new_width  = $img_width * $scale;
        $new_height = $img_height * $scale;

        $new_x      = ( $new_width  < $options['max_width'] ) ? ( $options['max_width'] - $new_width) / 2 : 0;
        $new_y      = ( $new_height < $options['max_height']) ? ( $options['max_height'] - $new_height) / 2 : 0;
        $new_img    = imagecreatetruecolor($options['max_width'], $options['max_height']);
        imagefilledrectangle($new_img, 0, 0, $options['max_width'], $options['max_height'], imagecolorallocate($new_img, 255, 255, 255));

        if (!$debug) {
            switch (strtolower(substr(strrchr($file_path, '.'), 1)))
            {
                case 'jpg':
                case 'jpeg':
                    $src_img = @imagecreatefromjpeg($file_path);
                    $write_image = 'imagejpeg';
                    break;
                case 'gif':
                    $src_img = @imagecreatefromgif($file_path);
                    $write_image = 'imagegif';
                    break;
                case 'png':
                    $src_img = @imagecreatefrompng($file_path);
                    $write_image = 'imagepng';
                    break;
                default:
                    $src_img = $image_method = null;
            }
        } else {
            switch (strtolower(substr(strrchr($file_path, '.'), 1)))
            {
                case 'jpg':
                case 'jpeg':
                    $src_img = imagecreatefromjpeg($file_path);
                    $write_image = 'imagejpeg';
                    break;
                case 'gif':
                    $src_img = imagecreatefromgif($file_path);
                    $write_image = 'imagegif';
                    break;
                case 'png':
                    $src_img = imagecreatefrompng($file_path);
                    $write_image = 'imagepng';
                    break;
                default:
                    $src_img = $image_method = null;
            }
        }

        if ($src_img == null) {
        //Ok, it couldn't load by normal means, try each one...
            $src_img = @imagecreatefromjpeg($file_path);
            if ($src_img == null) {
                $src_img = @imagecreatefromgif($file_path);
                if ($src_img == null) {
                    $src_img = @imagecreatefrompng($file_path);
                    if ($src_img == null) {
                        $src_img = @imagecreatefromstring(file_get_contents($file_path));
                    }
                }
            }
        }


        //OVERRIDE and always save a PNG
        $write_image = 'imagepng';

        $success = $src_img && @imagecopyresampled(
            $new_img,
            $src_img,
            $new_x,
            $new_y,
            0,
            0,
            $new_width,
            $new_height,
            $img_width,
            $img_height
        ) && $write_image($new_img, $new_file_path);
        // Free up memory (imagedestroy does not delete files):
        @imagedestroy($src_img);
        @imagedestroy($new_img);
        return $success;
    }

    /**
     * @param resource $image
     * @param string   $text
     * @param string   $font
     * @param int      $size
     * @param int      $angle
     * @return array
     */
    public static function imageTTFCenter($image, string $text, string $font, int $size, int $angle = 45): array
    {
        $xi = imagesx($image);
        $yi = imagesy($image);

        $box = imagettfbbox($size, $angle, $font, $text);

        $xr = abs(max($box[2], $box[4]));
        $yr = abs(max($box[5], $box[7]));

        $x = intval(($xi - $xr) / 2);
        $y = intval(($yi - $yr) / 2);

        return [$x, $y];
    }
}
