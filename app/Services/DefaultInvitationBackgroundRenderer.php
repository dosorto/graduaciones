<?php

namespace App\Services;

class DefaultInvitationBackgroundRenderer
{
    public function render(int $width = 1080, int $height = 1350): string
    {
        $canvas = $this->makeCanvas($width, $height);

        ob_start();
        imagepng($canvas, null, 9);
        $png = (string) ob_get_clean();

        imagedestroy($canvas);

        return $png;
    }

    public function makeCanvas(int $width = 1080, int $height = 1350)
    {
        $image = imagecreatetruecolor($width, $height);
        imagealphablending($image, true);
        imagesavealpha($image, true);

        $cream = imagecolorallocate($image, 251, 248, 241);
        $white = imagecolorallocate($image, 255, 255, 255);
        $navy = imagecolorallocate($image, 18, 39, 110);
        $navySoft = imagecolorallocatealpha($image, 33, 70, 168, 36);
        $gold = imagecolorallocate($image, 215, 167, 63);
        $goldSoft = imagecolorallocatealpha($image, 236, 196, 92, 64);
        $slateSoft = imagecolorallocatealpha($image, 15, 23, 42, 108);

        imagefilledrectangle($image, 0, 0, $width, $height, $cream);

        imagefilledellipse($image, (int) ($width * 0.15), (int) ($height * 0.14), (int) ($width * 0.8), (int) ($height * 0.58), $white);
        imagefilledellipse($image, (int) ($width * 0.95), (int) ($height * 0.26), (int) ($width * 0.72), (int) ($height * 0.64), $navySoft);
        imagefilledellipse($image, (int) ($width * 0.92), (int) ($height * 0.18), (int) ($width * 0.62), (int) ($height * 0.52), $goldSoft);
        imagefilledellipse($image, (int) ($width * 0.06), (int) ($height * 0.86), (int) ($width * 0.74), (int) ($height * 0.58), $navySoft);
        imagefilledellipse($image, (int) ($width * 0.02), (int) ($height * 0.82), (int) ($width * 0.62), (int) ($height * 0.48), $goldSoft);

        imagesetthickness($image, max(2, (int) round($width * 0.006)));
        imagearc($image, (int) ($width * 1.02), (int) ($height * 0.08), (int) ($width * 0.86), (int) ($height * 0.86), 80, 200, $navy);
        imagearc($image, (int) ($width * 1.04), (int) ($height * 0.1), (int) ($width * 0.74), (int) ($height * 0.74), 80, 196, $gold);
        imagearc($image, (int) ($width * -0.02), (int) ($height * 0.9), (int) ($width * 0.86), (int) ($height * 0.86), 260, 20, $navy);
        imagearc($image, (int) ($width * -0.04), (int) ($height * 0.92), (int) ($width * 0.74), (int) ($height * 0.74), 262, 16, $gold);

        $dotRadius = max(3, (int) round($width * 0.006));
        for ($i = 0; $i < 18; $i++) {
            $x = (int) ($width * 0.12) + ($i % 6) * (int) ($width * 0.055);
            $y = (int) ($height * 0.1) + intdiv($i, 6) * (int) ($width * 0.05);
            imagefilledellipse($image, $x, $y, $dotRadius * 2, $dotRadius * 2, $goldSoft);
        }

        for ($i = 0; $i < 16; $i++) {
            $x = (int) ($width * 0.68) + ($i % 4) * (int) ($width * 0.06);
            $y = (int) ($height * 0.78) + intdiv($i, 4) * (int) ($width * 0.055);
            imagefilledellipse($image, $x, $y, $dotRadius * 2, $dotRadius * 2, $slateSoft);
        }

        return $image;
    }
}
