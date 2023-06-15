<?php

namespace Learncom\Backend\Controllers;

use Learncom\Models\LearnExamSubject;
use Learncom\Repositories\ClassSubject;
use Learncom\Repositories\ExamSubject;
use Learncom\Repositories\Upload;
use Learncom\Utils\Validator;
use Phalcon\Paginator\Adapter\Model as PaginatorModel;
class ExamsucjectController extends ControllerBase
{
    public function readAction()
    {
        $current_page = $this->request->get('page');
        $validator = new Validator();
        if($validator->validInt($current_page) == false || $current_page < 1)
            $current_page=1;
        $sql = "SELECT * FROM Learncom\Models\LearnExamSubject WHERE 1";
        $arrParameter = array();

        $sql.=" ORDER BY subject_id DESC";
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
        $data = ['subject_order' => 1,'subject_parent_id' => ''];
        if($this->request->isPost()) {
            $messages = array();
            $data = array(
                'subject_parent_id' => $this->request->getPost("slcParentId", array('string', 'trim')),
                'subject_name' => $this->request->getPost("txtName", array('string', 'trim')),
                'subject_image' => $this->request->getPost("txtImage", array('string', 'trim')),
                'subject_order' => $this->request->getPost("txtOrder", array('string', 'trim')),
            );
            if ($this->request->hasFiles() == true && $_FILES['fileUpload']['size'] > 0)
            {
                $uploadFiles =[];
                $messages_upload = [];
                Upload::uploadFile($uploadFiles,$messages_upload);
                if ($messages_upload['type'] == "success") {
                    $data['subject_image'] = $uploadFiles[0]['file_url'];
                } else {
                    $messages['subject_image'] = $messages_upload['message'];
                }
            }
            if (empty($data['subject_name'])) {
                $messages['subject_name'] = 'Bạn cần nhập thông tin này';
            }
            if (empty($data['subject_order'])) {
                $messages['subject_order'] = 'Bạn cần nhập thông tin này';
            } elseif (!is_numeric($data['subject_order'])) {
                $messages['subject_order'] = 'Bạn cần nhập số';
            }
            if (count($messages) == 0) {
                $msg_result = array();
                $new_device = new LearnExamSubject();
                $result = $new_device->save($data);
                if ($result === true) {
                    $msg_result = array('status' => 'success', 'message' => 'Tạo môn học thành công');

                } else {
                    $message = "We can't store your info now: \n";
                    foreach ($new_device->getMessages() as $msg) {
                        $message .= $msg . "\n";
                    }
                    $msg_result['status'] = 'error';
                    $msg_result['message'] = $message;
                }

                $this->session->set('msg_result', $msg_result);
                return $this->response->redirect("/dashboard/list-exam-subject");
            }
        }
        $select_subject = ClassSubject::getComboboxParentSubject('',0,$data['subject_parent_id']);
        $messages["status"] = "border-red";
        $data['mode'] = 'Tạo';
        $this->view->setVars([
            'formData' => $data,
            'messages' => $messages,
            'select_subject' => $select_subject
        ]);
    }

    public function editAction()
    {
        $this->view->pick( $this->dispatcher->getControllerName().'/model');
        $id = $this->request->get('id');
        $gateway_model = ExamSubject::findFirstBySubjectId($id);
        if(empty($gateway_model))
        {
            return $this->response->redirect('notfound');
        }
        $data = $gateway_model->toArray();
        $messages = array();
        if($this->request->isPost()) {
            $data = array(
                'subject_parent_id' => $this->request->getPost("slcParentId", array('string', 'trim')),
                'subject_name' => $this->request->getPost("txtName", array('string', 'trim')),
                'subject_image' => $this->request->getPost("txtImage", array('string', 'trim')),
                'subject_order' => $this->request->getPost("txtOrder", array('string', 'trim')),
            );

            if (empty($data['subject_name'])) {
                $messages['subject_name'] = 'Bạn cần nhập thông tin này';
            }
            if (empty($data['subject_order'])) {
                $messages['subject_order'] = 'Bạn cần nhập thông tin này';
            } elseif (!is_numeric($data['subject_order'])) {
                $messages['subject_order'] = 'Bạn cần nhập số';
            }
            if (count($messages) == 0) {

                $result = $gateway_model->update($data);
                if ($result === true) {
                    $msg_result = array('status' => 'success', 'message' => 'Cập nhật môn học thành công');

                } else {
                    $message = "We can't store your info now: \n";
                    foreach ($gateway_model->getMessages() as $msg) {
                        $message .= $msg . "\n";
                    }
                    $msg_result['status'] = 'error';
                    $msg_result['message'] = $message;
                }

                $this->session->set('msg_result', $msg_result);
                return $this->response->redirect("/dashboard/list-exam-subject");
            }
        }
        $messages["status"] = "border-red";
        $select_subject = ClassSubject::getComboboxParentSubject('',0,$data['subject_parent_id']);
        $data['mode'] = 'Chỉnh Sửa';
        $this->view->setVars([
            'formData' => $data,
            'messages' => $messages,
            'select_subject' => $select_subject
        ]);
    }
    public function deleteAction()
    {
        $id = $this->request->get('id');
        $msg_result = array('error' => '', 'success' => '');
        $message = array();
        $type_model = ExamSubject::findFirstBySubjectId($id);
        if ($type_model) {
            if(count($message) > 0){
                $msg_result['message'] .= 'Can\'t delete the Subject: ' . $type_model->getSubjectName() . '. Because It\'s exists in: '.$message.'.<br>';
                $msg_result['status'] = 'error';
            }
            else {
                $message = 'Xóa môn học ' . $type_model->getSubjectName() . ' thành công';
                $msg_result['message'] = $message;
                $msg_result['status'] = 'success';
                $type_model->delete();
            }
        }
        $this->session->set('msg_result', $msg_result);
        return $this->response->redirect('/dashboard/list-exam-subject');
    }

}
