<?php

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\Label\Alignment\LabelAlignmentCenter;
use Endroid\QrCode\Label\Font\NotoSans;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;


require_once(__DIR__ . "/../vendor/autoload.php");
echo "123";exit;
getLabelQr();
function getLabelQr()
{
    $actual_link = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];

    $result = Builder::create()
        ->writer(new PngWriter())
        ->writerOptions([])
        ->data($actual_link)
        ->encoding(new Encoding('UTF-8'))
        ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
        ->size(300)
        ->margin(10)
        ->roundBlockSizeMode(new RoundBlockSizeModeMargin())
   //     ->logoPath( __DIR__.'/logo.png')
        ->labelText('Video của CHIBAO')
        ->labelFont(new NotoSans(20))
        ->labelAlignment(new LabelAlignmentCenter())
        ->validateResult(false)
        ->build();
    // Directly output the QR code
    header('Content-Type: ' . $result->getMimeType());
    echo $result->getString();

    // Save it to a file
    $result->saveToFile(__DIR__ . '/qrcode.png');

    // Generate a data URI to include image data inline (i.e. inside an <img> tag)
    $dataUri = $result->getDataUri();
    
}
