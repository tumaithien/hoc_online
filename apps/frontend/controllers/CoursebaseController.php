<?php

namespace Learncom\Frontend\Controllers;

use Learncom\Repositories\Group;
use Learncom\Repositories\Video;

/**
 * @property \GlobalVariable globalVariable
 * @property \My my
 */
class CoursebaseController extends ControllerBase
{

    protected $type; // video,document
    protected $list_chapter;
    protected $lesson;
    protected $chapter;
    protected $group_select;
    protected $lesson_select;
    public function initialize()
    {
        $this->group_select = $this->request->get('group'); //chapter in document
        $this->lesson_select = $this->request->get('lesson');
    }
    public function checkingAuth()
    {
        // var_dump($this->auth);exit;
        // if (!$this->auth) {
        //     return $this->response->redirect("/login");
        // }
        // if (!in_array($this->class_select, $this->class_id) || !in_array($this->subject_select, $this->subject_id)) {
        //     return $this->response->redirect("/permission");
        // }
    }
    public function getListGroup()
    {
        //ở video thì sẽ là group
        $this->list_chapter =  Group::findByClassAndSubjectAndCache($this->class_select, $this->subject_select, $this->type);
    }
    public function getListLesson()
    {
        foreach ($this->list_chapter as $chapter) {
            $this->lesson = Video::findByClassAndSubjectAndTypeAndGroupAndCache($this->class_select, $this->subject_select, $this->type, $chapter['group_id']);
            if (!empty($this->lesson)) {
                $this->chapter = $chapter;
                break;
            }
        }
    }
}
