<?php

namespace App\Support;

use BaconQrCode\Renderer\Color\Alpha;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\Fill;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Renderer\Color\Rgb;
use BaconQrCode\Writer;

class QrCodeSvg
{
    public static function generate(string $value, int $size = 220): string
    {
        return (new Writer(
            new ImageRenderer(
                new RendererStyle($size, 2, null, null, Fill::uniformColor(new Alpha(0, new Rgb(255, 255, 255)), new Rgb(15, 23, 42))),
                new SvgImageBackEnd()
            )
        ))->writeString($value);
    }
}
