<?php
namespace Learncom\Frontend\Controllers;

use Learncom\Repositories\CacheHistory;
use Learncom\Repositories\Chapter;
use Learncom\Repositories\ClassSubject;
use Learncom\Repositories\Document;
use Learncom\Repositories\Page;
use Learncom\Repositories\Video;

class DocumentController extends CoursebaseController
{
    public function indexAction()
    {
        $parent_keyword = 'lesson';
        $repoPage = new Page();
        $repoPage->AutoGenMetaPage('tailieu', 'Document');
        $key = "log_history_document" . $this->auth['id'];
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
        $this->getListLesson();
        $this->view->pick("course/index");


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
            'is_continue' => $is_continue,
        ]);
    }
    public function getListGroup()
    {
        $subject = ClassSubject::findFirstBySubjectId($this->subject_select);
        if ($subject) {
            $subject_parents_id = $subject->getSubjectParentId();
        }
        if ($subject_parents_id != 0) {
            $this->chapter_temp = Chapter::findByClassAndSubjectAndCache($this->class_select, $subject_parents_id);
        } else {
            $this->chapter_temp = Chapter::findByClassAndSubjectAndCache($this->class_select, $this->subject_select);
        }
        //ở video thì sẽ là group
    }
    public function getListLesson()
    {

        $this->getListGroup();
        foreach ($this->chapter_temp as $chapter) {
            $active_group = "";
            $lesson_temp = Document::findByClassAndSubjectAndChapterAndCache($this->class_select, $this->subject_select, $chapter['chapter_id']);
            if (!$this->group_select) {
                $this->group_select = $chapter['chapter_id'];
            }
            if ($chapter['chapter_id'] == $this->group_select) {
                $active_group = "active";
            }

            foreach ($lesson_temp as $lesson) {
                $active_lesson = "";
                if (!$this->lesson_select) {
                    $this->lesson_select = $lesson['document_id'];
                }
                if ($lesson['document_id'] == $this->lesson_select) {
                    $active_lesson = "active";
                }
                $this->lesson[$chapter['chapter_id']][] = [
                    'id' => $lesson['document_id'],
                    'name' => $lesson['document_name'],
                    'link' => $lesson['document_link'],
                    'active' => $active_lesson,
                    'href' => "/tailieu?classId={$this->class_select}&subjectId={$this->subject_select}&group={$chapter['chapter_id']}&lesson={$lesson['document_id']}"
                ];
                if ($this->lesson_select == $lesson['document_id'] && $this->group_select == $chapter['chapter_id']) {
                    $this->link = $lesson['document_link'];
                }

            }
            $this->list_chapter[] = [
                'id' => $chapter['chapter_id'],
                'name' => $chapter['chapter_name'],
                'active' => $active_group
            ];

        }
    }
}