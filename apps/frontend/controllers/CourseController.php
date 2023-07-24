<?php

namespace Learncom\Frontend\Controllers;

use Learncom\Repositories\CacheHistory;
use Learncom\Repositories\CacheRepo;
use Learncom\Repositories\Chapter;
use Learncom\Repositories\Group;
use Learncom\Repositories\Page;
use Learncom\Repositories\Video;

class CourseController extends CoursebaseController
{

    public function indexAction()
    {
        $parent_keyword = 'khoahoc';
        $this->type = "video";
        $repoPage = new Page();
        $repoPage->AutoGenMetaPage('khoahoc', 'Course');

        $key = "log_history_course" . $this->auth['id'];
        $cache = new CacheHistory($key);
        $is_continue = false;
        if (empty($this->group_select) || empty($this->lesson_select)) {
            $log = $cache->setLogCourse($this->class_select, $this->subject_select, $this->group_select, $this->lesson_select, false);
            if (!empty($log)) {
                $is_continue = true;
                $this->group_select = $log['group'] ?? '';
                $this->lesson_select = $log['lesson'] ?? '';
            }
        } else {
            $log = $cache->setLogCourse($this->class_select, $this->subject_select, $this->group_select, $this->lesson_select, true);
        }


        $this->checkingAuth();
        $this->getListGroup();
        $this->getListLesson();
        $this->view->setVars([
            // 'type' => $this->type,
            'parent_keyword' => $parent_keyword,
            'class_id' => $this->class_select,
            'subject_id' => $this->subject_select,
            'chapters' => $this->list_chapter,
            'lessons' => $this->lesson,
            'group_select' => $this->group_select,
            'lesson_select' => $this->lesson_select,
            'link' => $this->link,
            'is_continue' => $is_continue
        ]);
    }
    public function viewAction()
    {
        $chapter = $this->request->get("group_id");
        $lesson_id = $this->request->get("lesson_id");
    }
}