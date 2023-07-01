<?php

namespace Learncom\Frontend\Controllers;

// use Learncom\Frontend\Controllers\ControllerBase;

use Learncom\Models\LearnClass;
use Learncom\Repositories\CacheRepo;
use Learncom\Repositories\ClassSubject;
use Learncom\Repositories\Group;
use Learncom\Repositories\Video;

class CoursebaseController extends ControllerBase
{

    protected $type; // video,document
    protected $list_chapter;
    protected $lesson;
    protected $chapter;
    protected $group_select;
    protected $lesson_select;
    protected $chapter_temp;
    protected $link;
    protected $href;
    public function initialize()
    {
        parent::initialize();
        $this->group_select = $this->request->get('group'); //chapter in document
        $this->lesson_select = $this->request->get('lesson');
        $cache = new CacheRepo("class_subject_name_" . $this->class_select . "_" . $this->subject_select);
        $title_name = $cache->getCache();
        if (!$title_name) {
            $class_name = LearnClass::getNameById($this->class_select);
            $subject_name = ClassSubject::findFirstSubjectNameById($this->subject_select);
            $title_name = $subject_name . " " . $class_name;
            $title_name = $cache->setCache($title_name);
        }
        $this->view->title_name = $title_name;

    }
    public function checkingAuth()
    {
        // var_dump($this->auth);exit;
        if (!$this->auth) {
            return $this->response->redirect("/login");
        }
        if (!in_array($this->class_select, $this->class_id) || !in_array($this->subject_select, $this->subject_id)) {
            return $this->response->redirect("/permission");
        }
    }
    public function getListGroup()
    {
        //ở video thì sẽ là group
        $this->chapter_temp = Group::findByClassAndSubjectAndCache($this->class_select, $this->subject_select, $this->type);
    }
    public function getListLesson()
    {

        foreach ($this->chapter_temp as $chapter) {
            $active_group = "";

            $lesson_temp = Video::findAndCache($this->class_select, $this->subject_select, $this->type, $chapter['group_id']);
            if (!$this->group_select) {
                $this->group_select = $chapter['group_id'];
            }
            if ($chapter['group_id'] == $this->group_select) {
                $active_group = "active";
            }

            foreach ($lesson_temp as $lesson) {
                $active_lesson = "";
                if (!$this->lesson_select) {
                    $this->lesson_select = $lesson['video_id'];
                }
                if ($lesson['video_id'] == $this->lesson_select) {
                    $active_lesson = "active";
                }
                $this->lesson[$chapter['group_id']][] = [
                    'id' => $lesson['video_id'],
                    'name' => $lesson['video_name'],
                    'link' => $lesson['video_link'],
                    'active' => $active_lesson,
                    'href' => "/khoahoc?classId={$this->class_select}&subjectId={$this->subject_select}&group={$chapter['group_id']}&lesson={$lesson['video_id']}"
                ];
                if ($this->lesson_select == $lesson['video_id'] && $this->group_select == $chapter['group_id']) {
                    $this->link = $lesson['video_link'];
                }

            }
            $this->list_chapter[] = [
                'id' => $chapter['group_id'],
                'name' => $chapter['group_name'],
                'active' => $active_group
            ];

        }
    }
}