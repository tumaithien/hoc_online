<?php
namespace Learncom\Frontend\Controllers;

use Learncom\Repositories\Chapter;
use Learncom\Repositories\ClassSubject;
use Learncom\Repositories\Document;
use Learncom\Repositories\Page;
use Learncom\Repositories\Video;

class DocumentdifficultController extends ControllerBase
{
    public function indexAction()
    {
        $parent_keyword = 'lesson';
        $repoPage = new Page();
        $repoPage->AutoGenMetaPage('tailieu','Document');
        $class_id = $this->request->get('classId');
        $subject_id = $this->request->get('subjectId');
        if (!$this->auth){
            return $this->response->redirect("/login");
        }
        if (!in_array($class_id,$this->class_id) || !in_array($subject_id,$this->subject_id)) {
            return $this->response->redirect("/permission");
        }
        $subject_parents_id = 0;
        $subject = ClassSubject::findFirstBySubjectId($subject_id);
        if ($subject){
            $subject_parents_id = $subject->getSubjectParentId();
        }
        if ($subject_parents_id != 0){
            $listChapters = Chapter::findByClassAndSubject($class_id,$subject_parents_id);
        } else {
            $listChapters = Chapter::findByClassAndSubject($class_id,$subject_id);
        }

        $this->view->setVars([
            'parent_keyword' => $parent_keyword,
            'class_id' => $class_id,
            'subject_id' => $subject_id,
        ]);
    }
}