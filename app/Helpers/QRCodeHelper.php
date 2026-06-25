<?php

namespace App\Helpers;

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class QRCodeHelper
{
    public static function generate(string $data, int $size = 200): string
    {
        $options = new QROptions([
            'outputType' => QRCode::OUTPUT_IMAGE_PNG,
            'scale' => 5,
            'imageBase64' => true,
        ]);

        $qrCode = new QRCode($options);

        return $qrCode->render($data);
    }

    public static function generateSvg(string $data, int $size = 80): string
    {
        $options = new QROptions([
            'outputType' => QRCode::OUTPUT_MARKUP_SVG,
            'svgViewBoxSize' => $size,
            'addQuietzone' => true,
        ]);

        $qrCode = new QRCode($options);

        return $qrCode->render($data);
    }
}
