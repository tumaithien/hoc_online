<?php
namespace Learncom\Frontend\Controllers;

use Learncom\Repositories\Chapter;
use Learncom\Repositories\Page;
use Learncom\Repositories\Video;

class LessonController extends ControllerBase
{
    public function indexAction()
    {
        $parent_keyword = 'lesson';
        $type = 'baigiang';
        $repoPage = new Page();
        $repoPage->AutoGenMetaPage('lesson','Lesson');
        $class_id = $this->request->get('classId');
        $subject_id = $this->request->get('subjectId');
        if (!$this->auth){
            return $this->response->redirect("/login");
        }
        if (!in_array($class_id,$this->class_id) || !in_array($subject_id,$this->subject_id)) {
            return $this->response->redirect("/permission");
        }
        $listChapters = Chapter::findByClassAndSubject($class_id,$subject_id);
        $lessons = array();
        foreach ($listChapters as $chapter){
            $lessons = Video::findByClassAndSubjectAndTypeAndChapter($class_id,$subject_id,$type,$chapter->getChapterId());
            if (count($lessons) > 0){
                break;
            }
        }
        $group_id = $this->request->get('groupId');
        $video_id = $this->request->get('videoId');
        $group_id = $group_id ? $group_id : 0;
        $video_id = $video_id ? $video_id : 0;
        $current_url = $this->request->getURI();
        $base_url = explode("?",$current_url)[0];
        $base_url = $base_url."?classId=".$class_id."&subjectId=".$subject_id;

        $this->view->setVars([
            'type' => $type,
            'parent_keyword' => $parent_keyword,
            'class_id' => $class_id,
            'subject_id' => $subject_id,
            'lessons' => $lessons,
            'group_id' => $group_id,
            'video_id' => $video_id,
            'base_url' => $base_url,
        ]);
    }
}