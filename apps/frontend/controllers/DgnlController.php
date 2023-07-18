<?php

namespace Learncom\Frontend\Controllers;

use Learncom\Models\LearnDgnl;
use Learncom\Models\LearnTypeDgnl;
use Learncom\Repositories\CacheRepo;
use Learncom\Repositories\Chapter;
use Learncom\Repositories\Group;
use Learncom\Repositories\Page;
use Learncom\Repositories\Video;

class DgnlController extends ControllerBase
{
    public $dgnl_select;
    public $video_id;

    public function indexAction()
    {
        $parent_keyword = 'khoahoc';
        $this->type = "video";
        $repoPage = new Page();
        $repoPage->AutoGenMetaPage('khoahoc', 'Course');
        $arType = LearnTypeDgnl::find([
            'order' => "type_order ASC",
        ]);
        $this->view->setVars([
            // 'type' => $this->type,
            'parent_keyword' => $parent_keyword,
            'arType' => $arType->toArray(),
        ]);
    }
    public function viewAction()
    {
        $this->dgnl_select = $this->dispatcher->getParam('type_id');
        var_dump($this->dgnl_select);exit;
        $this->video_id = $this->request->get('video_id');

        if (!in_array($this->dgnl_select, $this->dgnl_type_id)) {
            return $this->response->redirect("/permission");
        }

        $parent_keyword = 'khoahoc';
        $this->type = "video";
        $repoPage = new Page();
        $repoPage->AutoGenMetaPage('khoahoc', 'Course');
        $arDgnlVideo = LearnDgnl::find([
            'dgnl_type_id = :type:',
            'bind' => [
                'type' => $this->dgnl_select
            ],
            'order' => "learn_category ASC,dgnl_order ASC"
        ])->toArray();
        var_dump($arDgnlVideo);exit;

        $this->view->setVars([
            // 'type' => $this->type,
            'parent_keyword' => $parent_keyword,
            'arDgnlVideo' => $arDgnlVideo,
            'video_selected' => $this->video_id,
        ]);
    }

}
