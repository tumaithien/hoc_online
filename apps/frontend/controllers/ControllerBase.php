<?php

namespace Learncom\Frontend\Controllers;

use Learncom\Models\LearnUser;
use Learncom\Repositories\Banner;
use Learncom\Repositories\CacheRepo;
use Learncom\Repositories\Config;
use Learncom\Repositories\Device;
use Learncom\Repositories\Info;
use Learncom\Repositories\Menu;
use Learncom\Repositories\User;
use Phalcon\Mvc\Controller;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\Label\Alignment\LabelAlignmentCenter;
use Endroid\QrCode\Label\Font\NotoSans;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeShrink;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Writer\SvgWriter;


/**
 * @property \GlobalVariable globalVariable
 * @property \My my
 */
class ControllerBase extends Controller
{
    protected $lang;
    protected $auth;
    protected $subject_id;
    protected $class_id;
    protected $group_subject_id;
    protected $school_class_id;
    protected $isMobile;
    public function initialize()
    {
        //current user
        $this->view->auth = $this->auth = $this->session->get('auth');
        if ($this->auth) {
            $user = new User();
            $user_auth = $user->findById($this->auth['id']);
            $this->class_id = $this->auth['class_ids'];
            $this->subject_id = $this->auth['subject_ids'];
            $this->group_subject_id = $this->auth['group_subject_id'];
            $this->view->group_subject_id = $this->group_subject_id;
            if (!$user_auth) {
                $this->response->redirect("logout");
            } else {
                $token_onile = $this->auth['online_token'];
                if ($token_onile) {
                    if ($user_auth->getUserTokenOnline() !== $token_onile) {
                        $this->response->redirect("logout");
                    }
                } else {
                    $this->response->redirect("logout");
                }
            }
        }
        $detect = new Device();
        $this->view->isMobile = $this->isMobile = $detect->isMobile() ? $detect->isMobile() : $detect->isTablet();
        $this->school_class_id = $this->auth['school_class_id'];
        $this->view->school_class_id = $this->auth['school_class_id'];
        $bannerTop = Banner::findBannerTop();
        if ($bannerTop) {
            $this->view->bannerTop = $bannerTop;
        }
        $menus = Menu::findMenu();
        if ($menus) {
            $this->view->menus = $menus;
        }
        $cacheClass = new CacheRepo('all_class');
        $arrClass = $cacheClass->getCache();
        if (!$arrClass) {
            $arrClass = \Learncom\Models\LearnClass::find([
                'order' => 'class_order ASC'
            ])->toArray();
            $arrClass = $cacheClass->setCache($arrClass);
        }

        $cacheSubject = new CacheRepo('all_subject');
        $arrSubject = $cacheSubject->getCache();
        if (!$arrSubject) {
            $arrSubject = \Learncom\Models\LearnSubject::find([
                'subject_parent_id = 0',
                'order' => 'subject_order ASC'
            ])->toArray();
            $arrSubject = $cacheSubject->setCache($arrSubject);
        }

        $this->view->arrSubject = $arrSubject;
        $this->view->arrClass = $arrClass;

        //     $current_url = $this->request->getURI();
        //   $current_url = "https://chibao.edu.vn". $current_url;
        //     $img_base64 = $this->getqrcode($current_url);
        //     $this->view->img_base64 = $img_base64;
    }
    public function getqrcode($actual_link)
    {
        include(__DIR__ . "/../../../vendor/autoload.php");

        $result = Builder::create()
            ->writer(new PngWriter())
            ->writerOptions([
                SvgWriter::WRITER_OPTION_EXCLUDE_XML_DECLARATION => true
            ])
            ->data($actual_link)

            ->encoding(new Encoding('ISO-8859-1'))
            ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
            ->size(300)
            ->margin(10)
            ->roundBlockSizeMode(new RoundBlockSizeModeShrink(1))
            ->foregroundColor(new Color(0, 0, 0))
            ->backgroundColor(new Color(255, 255, 255))
            ->logoPath(__DIR__ . '/../../../public/frontend/images/logo.jpeg')
            ->logoPunchoutBackground(true)
            ->logoResizeToWidth(100)
            ->logoResizeToHeight(100)


            ->labelText('https://chibao.edu.vn')
            ->labelFont(new NotoSans(20))
            ->labelAlignment(new LabelAlignmentCenter())
            ->validateResult(false)
            ->build();
        // Directly output the QR code
        //   header('Content-Type: ' . $result->getMimeType());

        $img = $result->getDataUri();
        return $img;
    }
}