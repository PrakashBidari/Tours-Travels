<?php

namespace App\Support;

use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

/** QR codes for booking confirmations (bacon-qr-code ships with Fortify). */
class QrCode
{
    public static function svg(string $text, int $size = 160): string
    {
        $svg = (new Writer(new ImageRenderer(new RendererStyle($size, 1), new SvgImageBackEnd)))->writeString($text);

        // Drop the XML prolog so the markup can be inlined in HTML.
        return trim(preg_replace('/^<\?xml.*?\?>/', '', $svg));
    }

    public static function dataUri(string $text, int $size = 160): string
    {
        return 'data:image/svg+xml;base64,'.base64_encode(static::svg($text, $size));
    }
}
