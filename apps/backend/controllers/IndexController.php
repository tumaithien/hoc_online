<?php

namespace Learncom\Backend\Controllers;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\Label\Alignment\LabelAlignmentCenter;
use Endroid\QrCode\Label\Font\NotoSans;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;

class IndexController extends ControllerBase
{

	public function indexAction()
	{

	}
	public function getqrcodeAction()
	{
	   require_once(__DIR__ . "/../../../vendor/autoload.php");
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
 //   header('Content-Type: ' . $result->getMimeType());
    $img= $result->getDataUri();
    die(json_encode($img));
	}
}