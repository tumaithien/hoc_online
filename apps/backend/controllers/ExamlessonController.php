<?php

namespace Learncom\Backend\Controllers;

use Learncom\Models\LearnExamLesson;
use Learncom\Models\LearnExamSubject;
use Learncom\Repositories\Chapter;
use Learncom\Repositories\ClassSubject;
use Learncom\Repositories\Document;
use Learncom\Repositories\ExamLesson;
use Learncom\Repositories\ExamSubject;
use Learncom\Utils\Validator;
use Phalcon\Paginator\Adapter\Model as PaginatorModel;
class ExamlessonController extends ControllerBase
{
    public function readAction()
    {
        $current_page = $this->request->get('page');
        $key_word= trim($this->request->get("txtSearchKeyword"));
        $class = trim($this->request->get("slcClass"));
        $subject_parent = trim($this->request->get("slcSubject"));
        $subject = trim($this->request->get("slcExamSubject"));
        $chapter = trim($this->request->get("slcChapter"));
        $validator = new Validator();
        if($validator->validInt($current_page) == false || $current_page < 1)
            $current_page=1;
        $sql = "SELECT * FROM Learncom\Models\LearnExamLesson WHERE 1";
        $arrParameter = array();
        if($key_word){
            $sql.=" AND lesson_name like CONCAT('%',:key_word:,'%') OR lesson_id = :key_word: ";
            $arrParameter['key_word']=$key_word;
            $this->dispatcher->setParam("key_word",$key_word);
        }
        if ($class) {
            $sql.= " AND lesson_class_id = :class:";
            $arrParameter['class'] = $class;
            $this->dispatcher->setParam("slcClass",$class);
        }
        if ($subject) {
            $sql.= " AND lesson_subject_id = :subject:";
            $arrParameter['subject']=$subject;
            $this->dispatcher->setParam("slcSubject",$subject);
        }
        if ($chapter) {
            $sql.= " AND lesson_chapter_id = :chapter:";
            $arrParameter['chapter'] = $chapter;
            $this->dispatcher->setParam("slcChapter",$chapter);
        }
        $sql.=" ORDER BY lesson_class_id,lesson_subject_id DESC";
        $list_gate = $this->modelsManager->executeQuery($sql,$arrParameter);
        $paginator = new PaginatorModel(array(
            'data'  => $list_gate,
            'limit' => 20,
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
        $select_subject = ClassSubject::getComboboxParentSubject('',0,$subject_parent);
        $this->view->setVars([
            'list_item' => $paginator->getPaginate(),
            'select_class' => $select_class,
            'select_subject' => $select_subject,
        ]);
    }


    public function createAction()
    {
        $this->view->pick( $this->dispatcher->getControllerName().'/model');
        $data = ['lesson_order' => 1,'lesson_class_id' => "",'lesson_subject_id' => "",'lesson_chapter_id' => "",'lesson_active' => "Y",'lesson_is_public' => "N"];
        if($this->request->isPost()) {
            $messages = array();
            $data = array(
                'lesson_name' => $this->request->getPost("txtName", array('string', 'trim')),
                'lesson_order' => $this->request->getPost("txtOrder", array('string', 'trim')),
                'lesson_class_id' => $this->request->getPost("slcClass", array('string', 'trim')),
                'lesson_subject_id' => $this->request->getPost("slcExamSubject", array('string', 'trim')),
                'lesson_active' => $this->request->getPost("radActive", array('string', 'trim')),
                'lesson_chapter_id' => $this->request->getPost("slcChapter", array('string', 'trim')),
            );

            if (empty($data['lesson_name'])) {
                $messages['lesson_name'] = 'Bạn cần nhập thông tin này';
            }
            if (empty($data['lesson_class_id'])) {
                $messages['lesson_class_id'] = 'Bạn cần nhập thông tin này';
            }
            if (empty($data['lesson_subject_id'])) {
                $messages['lesson_subject_id'] = 'Bạn cần nhập thông tin này';
            }
            if (empty($data['lesson_order'])) {
                $messages['lesson_order'] = 'Bạn cần nhập thông tin này';
            } elseif (!is_numeric($data['lesson_order'])) {
                $messages['lesson_order'] = 'Bạn cần nhập số';
            }
            if (count($messages) == 0) {
                $msg_result = array();
                $new_device = new LearnExamLesson();
                $result = $new_device->save($data);
                if ($result === true) {
                    $msg_result = array('status' => 'success', 'message' => 'Tạo Bài học thành công');

                } else {
                    $message = "We can't store your info now: \n";
                    foreach ($new_device->getMessages() as $msg) {
                        $message .= $msg . "\n";
                    }
                    $msg_result['status'] = 'error';
                    $msg_result['message'] = $message;
                }

                $this->session->set('msg_result', $msg_result);
                return $this->response->redirect("/dashboard/list-exam-lesson");
            }
        }
        $messages["status"] = "border-red";
        $data['mode'] = 'Tạo';
        $select_class = ClassSubject::getComboboxClass($data['lesson_class_id']);
        $select_subject = ClassSubject::getComboboxParentSubject('',0,"");
        $this->view->setVars([
            'formData' => $data,
            'messages' => $messages,
            'select_class' => $select_class,
            'select_subject' => $select_subject,
        ]);
    }

    public function editAction()
    {
        $this->view->pick( $this->dispatcher->getControllerName().'/model');
        $id = $this->request->get('id');
        $gateway_model = ExamLesson::findFirstById($id);
        if(empty($gateway_model))
        {
            return $this->response->redirect('notfound');
        }
        $data = $gateway_model->toArray();
        $messages = array();
        if($this->request->isPost()) {
            $data = array(
                'lesson_name' => $this->request->getPost("txtName", array('string', 'trim')),
                'lesson_order' => $this->request->getPost("txtOrder", array('string', 'trim')),
                'lesson_class_id' => $this->request->getPost("slcClass", array('string', 'trim')),
                'lesson_subject_id' => $this->request->getPost("slcExamSubject", array('string', 'trim')),
                'lesson_active' => $this->request->getPost("radActive", array('string', 'trim')),
                'lesson_chapter_id' => $this->request->getPost("slcChapter", array('string', 'trim')),
            );
            if (empty($data['lesson_name'])) {
                $messages['lesson_name'] = 'Bạn cần nhập thông tin này';
            }
            if (empty($data['lesson_class_id'])) {
                $messages['lesson_class_id'] = 'Bạn cần nhập thông tin này';
            }
            if (empty($data['lesson_subject_id'])) {
                $messages['lesson_subject_id'] = 'Bạn cần nhập thông tin này';
            }
            if (empty($data['lesson_order'])) {
                $messages['lesson_order'] = 'Bạn cần nhập thông tin này';
            } elseif (!is_numeric($data['lesson_order'])) {
                $messages['lesson_order'] = 'Bạn cần nhập số';
            }
            if (count($messages) == 0) {

                $result = $gateway_model->update($data);
                if ($result === true) {
                    $msg_result = array('status' => 'success', 'message' => 'Cập nhật Bài học thành công');

                } else {
                    $message = "We can't store your info now: \n";
                    foreach ($gateway_model->getMessages() as $msg) {
                        $message .= $msg . "\n";
                    }
                    $msg_result['status'] = 'error';
                    $msg_result['message'] = $message;
                }

                $this->session->set('msg_result', $msg_result);
                return $this->response->redirect("/dashboard/list-exam-lesson");
            }
        }
        $messages["status"] = "border-red";
        $data['mode'] = 'Chỉnh Sửa';
        $select_class = ClassSubject::getComboboxClass($data['lesson_class_id']);
        $select_subject = ClassSubject::getComboboxParentSubject('',0,"");
        $this->view->setVars([
            'formData' => $data,
            'messages' => $messages,
            'select_class' => $select_class,
            'select_subject' => $select_subject,
        ]);
    }
    public function deleteAction()
    {
        $id = $this->request->get('id');
        $msg_result = array('error' => '', 'success' => '');
        $type_model = ExamLesson::findFirstById($id);
        if ($type_model) {
            if($type_model->delete() == false){
                $msg_result['message'] .= 'Can\'t delete the Document: ' . $type_model->getLessonName();
                $msg_result['status'] = 'error';
            }
            else {
                $message = 'Xóa Bài học ' . $type_model->getLessonName() . ' thành công';
                $msg_result['message'] = $message;
                $msg_result['status'] = 'success';
            }
        }
        $this->session->set('msg_result', $msg_result);
        return $this->response->redirect('/dashboard/list-exam-lesson');
    }
    public function getlessonAction(){
        $chapter_id = $this->request->getPost('chapter_id');
        $exam_subject_id = $this->request->getPost('exam_subject_id');
        $class_id = $this->request->getPost('class_id');
        $lesson_id = $this->request->getPost('lesson_id');

        $html = ExamLesson::getCombobox($lesson_id,$chapter_id,$class_id);
        $subject_exam = ExamSubject::findFirstBySubjectId($exam_subject_id);
        $subject_id  = $subject_exam ? $subject_exam->getSubjectParentId() : "";
        $dataReturn = [
            'html' => $html,
            'subject_id' => $subject_id
        ];
        die(json_encode($dataReturn));

    }
}
