<?php

namespace Learncom\Backend\Controllers;

use Learncom\Repositories\ClassSubject;
use Learncom\Repositories\Exam;
use Learncom\Repositories\ExamGroup;
use Learncom\Repositories\ExamType;
use Learncom\Repositories\Upload;
use Learncom\Utils\Validator;
use Phalcon\Paginator\Adapter\Model as PaginatorModel;
class ManageexamController extends ControllerBase
{

    public function indexAction()
    {
        $current_page = $this->request->get('page');
        $class = trim($this->request->get("slcClass"));
        $subject = trim($this->request->get("slcSubject"));
        $exam_subject_id = trim($this->request->get("slcExamSubject"));
        $chapter_id = trim($this->request->get("slcChapter"));
        $lesson_id = trim($this->request->get("slcLesson"));
        $type_id = trim($this->request->get("slcType"));
        $validator = new Validator();
        $arrParameter = array();
        if($validator->validInt($current_page) == false || $current_page < 1)
            $current_page=1;
        $sql = "SELECT * FROM Learncom\Models\LearnExam as e
                  RIGHT JOIN Learncom\Models\LearnExamGroup as eg ON e.exam_group_id = eg.group_id
              WHERE 1";
        if ($class) {
            $sql.= " AND eg.group_class_id = :class:";
            $arrParameter['class'] = $class;
            $this->dispatcher->setParam("slcClass",$class);
        }
        if ($subject) {
            $sql.= " AND eg.group_subject_id = :subject:";
            $arrParameter['subject']=$subject;
            $this->dispatcher->setParam("slcSubject",$subject);
        }
        if ($exam_subject_id) {
            $sql.= " AND eg.group_exam_subject_id = :slcExamSubject:";
            $arrParameter['slcExamSubject']=$exam_subject_id;
            $this->dispatcher->setParam("slcExamSubject",$exam_subject_id);
        }
        if ($chapter_id) {
            $sql.= " AND eg.group_chapter_id = :slcChapter:";
            $arrParameter['slcChapter']=$chapter_id;
            $this->dispatcher->setParam("slcChapter",$chapter_id);
        }
        if ($lesson_id) {
            $sql.= " AND eg.group_lesson_id = :slcLesson:";
            $arrParameter['slcLesson']=$lesson_id;
            $this->dispatcher->setParam("slcLesson",$lesson_id);
        }
        if ($type_id) {
            $sql.= " AND eg.group_type_id = :type:";
            $arrParameter['type']=$type_id;
            $this->dispatcher->setParam("slcType",$type_id);
        }
        $sql.=" ORDER BY eg.group_id,e.exam_id DESC";
        $list_gate = $this->modelsManager->executeQuery($sql,$arrParameter);
        $paginator = new PaginatorModel(array(
            'data'  => $list_gate,
            'limit' => 8,
            'page'  => $current_page,
        ));
        if ($this->session->has('msg_result')) {
            $msg_result = $this->session->get('msg_result');
            $this->session->remove('msg_result');
            $this->view->msg_result = $msg_result;
        }
        if ($this->session->has('msg_result')) {
            $msg_result = $this->session->get('msg_result');
            $this->session->remove('msg_result');
            $this->view->msg_result = $msg_result;
        }
        $select_class = ClassSubject::getComboboxClass($class);
        $select_subject = ClassSubject::getComboboxParentSubject('',0,$subject);
        $select_type = ExamType::getCombobox($type_id);
        $this->view->setVars([
            'list_item' => $paginator->getPaginate(),
            'select_class' => $select_class,
            'select_subject' => $select_subject,
            'select_type' => $select_type
        ]);
    }
    public function deleteAction()
    {
        $id = $this->request->get('id');
        $msg_result = array('status' => '', 'message' => '');
        $type_model = Exam::findFirstById($id);
        if ($type_model) {
            if($type_model->delete() == false){
                $msg_result['message'] .= 'Can\'t delete the Câu hỏi: ' . $type_model->getExamId();
                $msg_result['status'] = 'danger';
            }
            else {
                Upload::deleteDirExam($type_model->getExamLinkTest(),$type_model->getExamAnswer(),true);
                ExamGroup::deleteExam($id);
                $message = 'Xóa Câu hỏi' . $type_model->getExamId() . ' thành công';
                $msg_result['message'] = $message;
                $msg_result['status'] = 'success';
            }
        }
        $this->session->set('msg_result', $msg_result);
        return $this->response->redirect('/dashboard/list-exam');
    }
}