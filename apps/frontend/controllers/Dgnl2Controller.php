<?php

namespace Learncom\Frontend\Controllers;

use Learncom\Repositories\CacheRepo;
use Learncom\Repositories\Chapter;
use Learncom\Repositories\Group;
use Learncom\Repositories\Page;
use Learncom\Repositories\Video;

class Dgnl2Controller extends ControllerBase
{

    public function indexAction()
    {
        $parent_keyword = 'khoahoc';
        $this->type = "video";
        $repoPage = new Page();
        $repoPage->AutoGenMetaPage('khoahoc', 'Course');

        if (!$this->auth) {
            return $this->response->redirect("/login");
        }

        $this->view->setVars([
            // 'type' => $this->type,
            'parent_keyword' => $parent_keyword,
            'class_id' => $this->class_select,
            'subject_id' => $this->subject_select,
        ]);
    }
    public function viewAction()
    {

    }

}