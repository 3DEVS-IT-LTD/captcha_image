<?php

namespace ThreeDevs\CaptchaImage;

abstract class CaptchaImage
{

    public static string $session_name = 'threedevs_captcha_image';
    public static function getCaptchaImage($id = null, int $imgWidth = 400, int $imgHeight = 150): array
    {
        $id = $id ? $id : time();

        $random = substr(md5(rand()), 0, 7);

        $_SESSION[self::$session_name][$id] = str_split($random);

        $font = __DIR__.DIRECTORY_SEPARATOR.'fonts'.DIRECTORY_SEPARATOR.'arial.ttf';

        $image = imagecreatetruecolor($imgWidth, $imgHeight);
        $black = imagecolorallocatealpha($image, 0, 0, 0, 90);
        $color = imagecolorallocate($image, 200, 100, 90); // red
        $red_line = imagecolorallocatealpha($image, 200, 100, 90, 90);
        $white = imagecolorallocate($image, 255, 255, 255);
        $font_colors = array(
            imagecolorallocate($image, 255, 0, 0),
            imagecolorallocate($image, 255, 0, 92),
            imagecolorallocate($image, 253, 0, 255),
            imagecolorallocate($image, 65, 0, 255),
            imagecolorallocate($image, 0, 159, 255),
            imagecolorallocate($image, 0, 255, 80),
            imagecolorallocate($image, 255, 108, 0),
        );
        imagefilledrectangle($image, 0, 0, $imgWidth, $imgHeight, $white);
        $mod = 10;
        $totalChars = count($_SESSION['captcha_code'][$id]);
        $eachCharSpace = ($imgWidth - $mod) / $totalChars;

        foreach ($_SESSION['captcha_code'][$id] as $i => $v) {
            $thisColor = mt_rand(0, count($font_colors) - 1);
            $thisColor = $thisColor < 0 || $thisColor == count($font_colors) ? count($font_colors) - 1 : $thisColor;
            $mod = $i ? $mod += $eachCharSpace : $mod;
            $angle = mt_rand(300, 400);
            $size = mt_rand(20, 40);
            $height = mt_rand(40, 70);
            $x1 = mt_rand(0, 300);
            $y1 = mt_rand(0, 100);
            $x2 = mt_rand(0, 300);
            $y2 = mt_rand(10, 100);
            $thickness = mt_rand(1, 5);
            $center_rand1 = mt_rand(1, 300);
            $center_rand2 = mt_rand(1, 300);
            $start_rand1 = mt_rand(10, 300);
            $start_rand2 = mt_rand(10, 300);
            $arc_width = mt_rand(10, 300);
            $arc_height = mt_rand(10, 300);
            imageline($image, $x1, $y1, $x2, $y2, $black);
            imagearc($image, $center_rand1, $center_rand2, $arc_width, $arc_height, $start_rand1, $start_rand2, $red_line);
            imagesetthickness($image, $thickness);

            imagettftext($image, $size, $angle, $mod, $height, $font_colors[$thisColor], $font, $v);
        }

        // Start output buffering
        ob_start();
        imagepng($image);
        $data = ob_get_contents();
        ob_end_clean();

        // Destroy the image resource
        imagedestroy($image);

        return [
            'image' => 'data:image/png;base64,' . base64_encode($data),
            'id' => $id,
            'width' => $imgWidth,
            'height' => $imgHeight,
        ];
    }

    public static function verifyCaptcha($id, string $user_input): bool
    {

    }
}