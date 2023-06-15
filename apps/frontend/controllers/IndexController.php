<?php
namespace Learncom\Frontend\Controllers;

use Learncom\Repositories\Article;
use Learncom\Repositories\Banner;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\Label\Alignment\LabelAlignmentCenter;
use Endroid\QrCode\Label\Font\NotoSans;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\BinaryWriter;
use Endroid\QrCode\Color\Color;

class IndexController extends ControllerBase
{
    public function indexAction()
    {
        $bannerRepo = new Banner();
        $banner = $bannerRepo->findBanner();
        $blogRepo = new Article();
        $blogs = $blogRepo->getHomeArticle();
        $blog_keyword = 'blogs';
        $this->view->setVars([
            'banners' => $banner,
            'blogs' => $blogs,
            'blog_keyword' => $blog_keyword
        ]);
    }
    public function getqrcodeAction()
    {
        include(__DIR__ . "/../../../vendor/autoload.php");
        // $actual_link = $this->request->get("url");
        // if (!$actual_link) {
        //     die(json_endcode(["Not found link"]));
        // }
        $actual_link = "https://chibao.edu.vn/qr-code";
          $result = Builder::create()
            ->writer(new BinaryWriter())
            ->writerOptions([])
            ->data($actual_link)
            
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
            ->size(300)
            ->margin(10)
            ->roundBlockSizeMode(new RoundBlockSizeModeMargin())
            // ->foregroundColor(new Color(0, 0, 0))
            // ->backgroundColor(new Color(255, 255, 255))
                 ->logoPath( __DIR__.'/../../../public/frontend/images/logo.jpeg')
                 ->logoPunchoutBackground(true)
                  ->logoResizeToWidth(100)
                  ->logoResizeToHeight(100)
                 
                 
            ->labelText('https://chibao.edu.vn')
            ->labelFont(new NotoSans(20))
            ->labelAlignment(new LabelAlignmentCenter())
            ->validateResult(false)
            ->build();
header('Content-Type: '.$result->getMimeType());
echo $result->getString();

// Save it to a file
// $result->saveToFile(__DIR__.'/qrcode.png');

// Generate a data URI to include image data inline (i.e. inside an <img> tag)
$dataUri = $result->getDataUri();
    }
    function require_files_recursive($dir, $level = 0)
    {
        if (!is_dir($dir)) {
            return;
        }

        // Require all PHP files in the directory
        foreach (glob("$dir/*.php") as $file) {
            require_once $file;
        }

        // Recursively require files in subdirectories up to level 4
        if ($level < 4) {
            foreach (glob("$dir/*", GLOB_ONLYDIR) as $subdir) {
                require_files_recursive($subdir, $level + 1);
            }
        }
    }

}