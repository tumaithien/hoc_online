<?php

namespace Learncom\Frontend\Controllers;

use Learncom\Repositories\CacheRepo;
use Learncom\Repositories\Chapter;
use Learncom\Repositories\Group;
use Learncom\Repositories\Page;
use Learncom\Repositories\Video;

class CourseController extends ControllerBase
{
    
    public function indexAction()
    {
        $parent_keyword = 'khoahoc';
        // $this->type = "video";
        $repoPage = new Page();
        $repoPage->AutoGenMetaPage('khoahoc', 'Course');

        // $this->checkingAuth();
        // $this->getListGroup();
        // $this->getListLesson();
        $this->view->setVars([
            // 'type' => $this->type,
            'parent_keyword' => $parent_keyword,
            'class_id' => $this->class_select,
            'subject_id' => $this->subject_select,
            // 'chapters' => $this->list_chapter,
            // 'lessons' => $this->lesson,
        ]);
    }
    public function viewAction()
    {
        $chapter = $this->request->get("group_id");
        $lesson_id = $this->request->get("lesson_id");
    }
}
