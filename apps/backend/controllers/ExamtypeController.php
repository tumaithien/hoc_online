<?php

namespace Learncom\Backend\Controllers;

use Learncom\Models\LearnExamGroup;
use Learncom\Models\LearnExamType;
use Learncom\Models\LearnTest;
use Learncom\Repositories\ClassSubject;
use Learncom\Repositories\Exam;
use Learncom\Repositories\ExamGroup;
use Learncom\Repositories\ExamLesson;
use Learncom\Repositories\ExamSubject;
use Learncom\Repositories\ExamType;
use Learncom\Repositories\Test;
use Learncom\Repositories\Upload;
use Learncom\Utils\Validator;
use Phalcon\Paginator\Adapter\Model as PaginatorModel;
class ExamtypeController extends ControllerBase
{
    public function readAction()
    {
        $current_page = $this->request->get('page');
        $validator = new Validator();
        if($validator->validInt($current_page) == false || $current_page < 1)
            $current_page=1;
        $sql = "SELECT * FROM Learncom\Models\LearnExamType WHERE 1";
        $arrParameter = array();

        $sql.=" ORDER BY type_id DESC";
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
        $this->view->list_item = $paginator->getPaginate();
    }


    public function createAction()
    {
        $this->view->pick( $this->dispatcher->getControllerName().'/model');
        $data = ['type_order' => 1];
        if($this->request->isPost()) {
            $messages = array();
            $data = array(
                'type_name' => $this->request->getPost("txtName", array('string', 'trim')),
                'type_order' => $this->request->getPost("txtOrder", array('string', 'trim')),
            );
            if (empty($data['type_name'])) {
                $messages['type_name'] = 'Bạn cần nhập thông tin này';
            }
            if (empty($data['type_order'])) {
                $messages['type_order'] = 'Bạn cần nhập thông tin này';
            } elseif (!is_numeric($data['type_order'])) {
                $messages['type_order'] = 'Bạn cần nhập số';
            }
            if (count($messages) == 0) {
                $msg_result = array();
                $new_device = new LearnExamType();
                $result = $new_device->save($data);
                if ($result === true) {
                    $msg_result = array('status' => 'success', 'message' => 'Tạo loại đề thi thành công');

                } else {
                    $message = "We can't store your info now: \n";
                    foreach ($new_device->getMessages() as $msg) {
                        $message .= $msg . "\n";
                    }
                    $msg_result['status'] = 'error';
                    $msg_result['message'] = $message;
                }

                $this->session->set('msg_result', $msg_result);
                return $this->response->redirect("/dashboard/list-exam-type");
            }
        }
        $messages["status"] = "border-red";
        $data['mode'] = 'Tạo';
        $this->view->setVars([
            'formData' => $data,
            'messages' => $messages,
        ]);
    }

    public function editAction()
    {
        $this->view->pick( $this->dispatcher->getControllerName().'/model');
        $id = $this->request->get('id');
        $gateway_model = LearnExamType::findFirst([
            'type_id = :ID:',
            'bind' => ['ID' => $id]
        ]);
        if(empty($gateway_model))
        {
            return $this->response->redirect('notfound');
        }
        $data = $gateway_model->toArray();
        $messages = array();
        if($this->request->isPost()) {
            $data = array(
                'type_name' => $this->request->getPost("txtName", array('string', 'trim')),
                'type_order' => $this->request->getPost("txtOrder", array('string', 'trim')),
            );
            if (empty($data['type_name'])) {
                $messages['type_name'] = 'Bạn cần nhập thông tin này';
            }
            if (empty($data['type_order'])) {
                $messages['type_order'] = 'Bạn cần nhập thông tin này';
            } elseif (!is_numeric($data['type_order'])) {
                $messages['type_order'] = 'Bạn cần nhập số';
            }
            if (count($messages) == 0) {

                $result = $gateway_model->update($data);
                if ($result === true) {
                    $msg_result = array('status' => 'success', 'message' => 'Cập nhật loại đề thi thành công');

                } else {
                    $message = "We can't store your info now: \n";
                    foreach ($gateway_model->getMessages() as $msg) {
                        $message .= $msg . "\n";
                    }
                    $msg_result['status'] = 'error';
                    $msg_result['message'] = $message;
                }

                $this->session->set('msg_result', $msg_result);
                return $this->response->redirect("/dashboard/list-exam-type");
            }
        }
        $messages["status"] = "border-red";
        $data['mode'] = 'Chỉnh sửa';
        $this->view->setVars([
            'formData' => $data,
            'messages' => $messages,
        ]);
    }
    public function deleteAction()
    {
        $id = $this->request->get('id');
        $msg_result = array('error' => '', 'success' => '');
        $message = array();
        $type_model =  $gateway_model = LearnExamType::findFirst([
            'type_id = :ID:',
            'bind' => ['ID' => $id]
        ]);
        if ($type_model) {
            $type_model->delete();
            $group = LearnExamGroup::find([
                'group_type_id = :type_id:',
                'bind' => ['type_id' => $id],
            ]);
            $total = 0;
            foreach ($group as $item) {
                $data = explode(',',$item->getGroupDataTest());
                foreach ($data as $value) {
                    $exam = Exam::findFirstById($value);
                    if ($exam) {
                        if(file_exists(__DIR__."/../../..".$exam->getExamLinkTest()) && $exam->getExamLinkTest()!="" && $exam->getExamLinkTest()!=null) {
                            unlink(__DIR__."/../../..".$exam->getExamLinkTest());
                        }
                        if(file_exists(__DIR__."/../../..".$exam->getExamLinkAnswer()) && $exam->getExamLinkAnswer()!="" && $exam->getExamLinkAnswer()!=null) {
                            unlink(__DIR__."/../../..".$exam->getExamLinkAnswer());
                        }
                        $exam->delete();
                        $total++;
                    }
                }
                $item->delete();
            }
            $message = 'Xóa Nhóm đề thi ' . $type_model->getTypeName() . ' thành công'."và Xóa thành công ".$total." Câu hỏi của nhóm ";
            $msg_result['message'] = $message;
            $msg_result['status'] = 'success';

        }
        $this->session->set('msg_result', $msg_result);
        return $this->response->redirect('/dashboard/list-exam-type');
    }

    public function createtestAction() {
        $messages = [];
        $msg_result = [];
        $data = array(
            'group_class_id' => $this->request->get("slcClass", array('string', 'trim')),
            'group_subject_id' => $this->request->get("slcSubject", array('string', 'trim')),
            'group_exam_subject_id' => $this->request->get("slcExamSubject", array('string', 'trim')),
            'group_chapter_id' => $this->request->get("slcChapter", array('string', 'trim')),
            'group_lesson_id' => $this->request->get("slcLesson", array('string', 'trim')),
            'group_type_id' => $this->request->get("slcType", array('string', 'trim')),
            'lock' => $this->request->get("lock", array('string', 'trim')),
        );
        if ($data['group_lesson_id']) {
            $lesson_model = ExamLesson::findFirstById($data['group_lesson_id']);
            if ($lesson_model) {
                $data['group_class_id'] = $lesson_model->getLessonClassId();
                $data['group_exam_subject_id'] =  $lesson_model->getLessonSubjectId();
                $data['group_chapter_id'] = $lesson_model->getLessonChapterId();
                $exam_subject_model = ExamSubject::findFirstBySubjectId($data['group_exam_subject_id']);
                $data['group_subject_id'] = $exam_subject_model ? $exam_subject_model->getSubjectId() : 0 ;
            }

        }
        if($this->request->isPost()) {
            $data = array(
                'group_class_id' => $this->request->getPost("slcClass", array('string', 'trim')),
                'group_number_question' => $this->request->getPost("txtNumberQuestion", array('string', 'trim')),
                'group_subject_id' => $this->request->getPost("slcSubject", array('string', 'trim')),
                'group_exam_subject_id' => $this->request->getPost("slcExamSubject", array('string', 'trim')),
                'group_chapter_id' => $this->request->getPost("slcChapter", array('string', 'trim')),
                'group_lesson_id' => $this->request->getPost("slcLesson", array('string', 'trim')),
                'group_answer_no' => $this->request->getPost("txtAnswer", array('string', 'trim')),
                'group_type_id' => $this->request->getPost("slcType", array('string', 'trim')),
            );
            if (empty($data['group_class_id'])) {
                $messages['group_class_id'] = 'Bạn cần nhập thông tin này';
            }
            if (empty($data['group_subject_id'])) {
                $messages['group_subject_id'] = 'Bạn cần nhập thông tin này';
            }
            if (empty($data['group_exam_subject_id'])) {
                $messages['group_exam_subject_id'] = 'Bạn cần nhập thông tin này';
            }
            if (empty($data['group_chapter_id'])) {
                $messages['group_chapter_id'] = 'Bạn cần nhập thông tin này';
            }
            if (empty($data['group_lesson_id'])) {
                $messages['group_lesson_id'] = 'Bạn cần nhập thông tin này';
            }
            if ($_FILES['fileUpload']['size'][0] <= 0) {
                $messages['image'] = 'Bạn cần upload hình ảnh câu hỏi.';
            }
            if (empty($data['group_number_question'])) {
                $messages['group_number_question'] = 'Bạn cần nhập thông tin này';
            } elseif (!is_numeric($data['group_number_question'])) {
                $messages['group_number_question'] = 'Bạn cần nhập số';
            } elseif (count($_FILES['fileUpload']['size']) != $data['group_number_question']) {
                $messages['group_number_question'] = 'Số ảnh cho câu hỏi không khớp với số câu hỏi';
            }
            $answer = $this->recoveAnswer($data['group_answer_no']);
            $arrAnswer = Test::stringToArrAnswer($answer);
            if (count($messages) == 0) {
                $message_image = "";
                $arrID = [];
                $group = ExamGroup::findFirstByAll($data['group_class_id'],$data['group_subject_id'],$data['group_exam_subject_id'],$data['group_chapter_id'],$data['group_lesson_id'],$data['group_type_id']);
                if (!$group) {
                    $group = new LearnExamGroup();
                    $group->setGroupClassId($data['group_class_id']);
                    $group->setGroupSubjectId($data['group_subject_id']);
                    $group->setGroupExamSubjectId($data['group_exam_subject_id']);
                    $group->setGroupChapterId($data['group_chapter_id']);
                    $group->setGroupLessonId($data['group_lesson_id']);
                    $group->setGroupTypeId($data['group_type_id']);
                    $group->save();
                }
                if ($this->request->hasFiles() == true && $_FILES['fileUpload']['size'][0] > 0 )
                {
                    $total = 0;
                    foreach ($_FILES['fileUpload']['name'] as $item => $value) {
                        $messages_upload = [];
                        Upload::uploadFileMultiForExam($arrID,$messages_upload,$item,$group->getGroupId(),$arrAnswer);
                        if ($messages_upload['type'] == "success") {
                            $total++;
                        }
                    }
                    if ($total < count($_FILES['fileUpload']['name'])) {
                        $message_image = "Upload hình ảnh bị lỗi";
                    }
                }
                if ($group->getGroupDataTest()) {
                    $arrExamId = explode(',',$group->getGroupDataTest());
                    $arrExamId = array_merge($arrExamId,$arrID);
                    $arrExamId = array_unique($arrExamId);
                } else {
                    $arrExamId = $arrID;
                }
                $group->setGroupDataTest(implode(',',$arrExamId));
                $result = $group->save();
                if ($result && empty($message_image)) {
                    $msg_result = [
                        'status' => 'success',
                        'message' => 'Bạn đã nạp vào kho đề thành công '.$data['group_number_question'] . " câu hỏi.",
                    ];
                } else {
                    $msg_result = [
                        'status' => 'error',
                        'message' => $message_image
                    ];
                }
            }
        }
        
        $select_class = ClassSubject::getComboboxClass($data['group_class_id']);
        $select_subject = ClassSubject::getComboboxParentSubject('',0,$data['group_subject_id']);
        $select_type = ExamType::getCombobox($data['group_type_id']);
        $messages["status"] = "border-red";
        $this->view->setVars([
            'formData' => $data,
            'messages' => $messages,
            'select_class' => $select_class,
            'select_subject' => $select_subject,
            'msg_result' => $msg_result,
            'select_type' => $select_type
        ]);
    }
    private function recoveAnswer($test) {
        $test = str_replace('&#160;','',$test);
        $test = str_replace(';','',$test);
        while (strpos($test, "\r") !== false ) {
            $test = str_replace("\r","",$test);
        }
        while (strpos($test, "\n") !== false ) {
            $test = str_replace("\n","",$test);
        }
        while (strpos($test, '  ') !== false ) {
            $test = str_replace('  ',' ',$test);
        }
        $test = trim($test);
        $test = str_replace('.',':',$test);
        $test = str_replace(' ',',',$test);
        return $test;
    }

}
