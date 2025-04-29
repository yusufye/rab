<?php

namespace App\Helpers;

use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class QrCodeHelper
{
    public static function generateQrCode($text)
    {
        $renderer = new ImageRenderer(
            new RendererStyle(300), // ukuran QR Code
            new SvgImageBackEnd()
        );

        $writer = new Writer($renderer);
        $svg = $writer->writeString($text); // Hasilkan SVG

        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }
}