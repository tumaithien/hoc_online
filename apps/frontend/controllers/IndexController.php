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
use Learncom\Models\LearnClass;
use Learncom\Models\LearnSubject;
use Learncom\Models\LearnUser;
use Learncom\Repositories\CacheRepo;
use Learncom\Repositories\Document;
use Learncom\Repositories\User;
use Learncom\Repositories\Video;

class IndexController extends ControllerBase
{
    public function indexAction()
    {
        $bannerRepo = new Banner();
        $blog_keyword = 'blogs';

        $banner = $bannerRepo->findBanner();
        $video = $bannerRepo->findVideoIndex();
        $blogRepo = new Article();
        $blogs = $blogRepo->getHomeArticle();
        $total_user = User::countUser();
        $total_document = Document::count();
        $total_video = Video::count();
        //môn toán subject_id = 5
        $videos = Video::findHomeVideo($this->allSubject,$this->allClass);
        $arrClassId = array_unique(array_column($videos,"video_class_id"));
        $this->view->setVars([
            'video_intro' => $video[0]['banner_image'] ?? "",
            'banners' => $banner,
            'blogs' => $blogs,
            'blog_keyword' => $blog_keyword,
            'videos' => $videos,
            'total_user' => $total_user,
            'total_document' => $total_document,
            'total_video' => $total_video,
            'arrClassId' => $arrClassId
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
            ->logoPath(__DIR__ . '/../../../public/frontend/images/logo.jpeg')
            ->logoPunchoutBackground(true)
            ->logoResizeToWidth(100)
            ->logoResizeToHeight(100)


            ->labelText('https://chibao.edu.vn')
            ->labelFont(new NotoSans(20))
            ->labelAlignment(new LabelAlignmentCenter())
            ->validateResult(false)
            ->build();
        header('Content-Type: ' . $result->getMimeType());
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