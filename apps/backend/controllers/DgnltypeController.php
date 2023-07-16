<?php

namespace Learncom\Backend\Controllers;

use Learncom\Models\LearnTypeDgnl;
use Learncom\Models\LearnVideo;
use Learncom\Repositories\ClassSubject;
use Learncom\Repositories\Upload;
use Learncom\Repositories\Video;
use Learncom\Utils\Validator;
use Phalcon\Paginator\Adapter\Model as PaginatorModel;

class DgnltypeController extends ControllerBase
{
    public function readAction()
    {
        $current_page = $this->request->get('page');
        $key_word = trim($this->request->get("txtSearchKeyword"));

        $validator = new Validator();
        if ($validator->validInt($current_page) == false || $current_page < 1)
            $current_page = 1;
        $sql = "SELECT * FROM Learncom\Models\LearnTypeDgnl";
        $arrParameter = array();
        if ($key_word) {
            $sql .= " AND (type_name like CONCAT('%',:key_word:,'%') OR type_id = :key_word:  OR type_teacher like CONCAT('%',:key_word:,'%') )";
            $arrParameter['key_word'] = $key_word;
            $this->dispatcher->setParam("key_word", $key_word);
        }

        $sql .= " ORDER BY type_id DESC";
        $list_gate = $this->modelsManager->executeQuery($sql, $arrParameter);
        $paginator = new PaginatorModel(
            array(
                'data' => $list_gate,
                'limit' => 20,
                'page' => $current_page,
            )
        );
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

        $this->view->setVars([
            'list_item' => $paginator->getPaginate(),

        ]);
    }


    public function createAction()
    {
        $this->view->pick($this->dispatcher->getControllerName() . '/model');
        $data = ['type_order' => 1, 'type_class_id' => "", 'type_subject_id' => "", 'type_active' => "Y"];
        if ($this->request->isPost()) {
            $messages = array();
            $data = array(
                'type_name' => $this->request->getPost("txtName", array('string', 'trim')),
                'type_order' => $this->request->getPost("txtOrder", array('string', 'trim')),
                'type_teacher' => $this->request->getPost("txtTeacher", array('string', 'trim')),
                'type_descriptiont' => $this->request->getPost("txtDescriotiont"),
                'type_content' => $this->request->getPost("txtContent"),

            );

            if (empty($data['type_name'])) {
                $messages['type_name'] = 'Bạn cần nhập thông tin này';
            }

            if (empty($data['type_order'])) {
                $messages['type_order'] = 'Bạn cần nhập thông tin này';
            } elseif (!is_numeric($data['type_order'])) {
                $messages['type_order'] = 'Bạn cần nhập số';
            }
            $uploadFilesType = array();
            $uploadFilesTeacher = array();

            $error = [];
            $messagesUpload = [];
            // Check if the user has uploaded files
            if ($this->request->hasFiles() == true) {
                Upload::uploadFile($uploadFilesType, $messagesUpload, 'fileUpload-1');
                Upload::uploadFile($uploadFilesTeacher, $messagesUpload, 'fileUpload-2');
            }
            var_dump($messagesUpload);
            exit;

            $this->view->uploadFiles = $uploadFilesType;
            if (!empty($uploadFilesType)) {
                $data['type_image'] = $uploadFilesType[0]['file_url'];
            }
            if (!empty($uploadFilesTeacher)) {
                $data['type_icon'] = $uploadFilesTeacher[0]['file_url'];
            }
            if (count($messages) == 0) {
                $msg_result = array();
                $new_device = new LearnTypeDgnl();
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
                return $this->response->redirect("/dashboard/list-type-dgnl");
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
        $this->view->pick($this->dispatcher->getControllerName() . '/model');
        $id = $this->request->get('id');
        $gateway_model = LearnTypeDgnl::findFirstById($id);
        if (empty($gateway_model)) {
            return $this->response->redirect('notfound');
        }
        $data = $gateway_model->toArray();
        $messages = array();
        if ($this->request->isPost()) {
            $messages = array();

            $data['type_name'] = $this->request->getPost("txtName", array('string', 'trim'));
            $data['type_order'] = $this->request->getPost("txtOrder", array('string', 'trim'));
            $data['type_teacher'] = $this->request->getPost("txtTeacher", array('string', 'trim'));
            $data['type_descriptiont'] = $this->request->getPost("txtDescriotiont");
            $data['type_content'] = $this->request->getPost("txtContent");

            if (empty($data['type_name'])) {
                $messages['type_name'] = 'Bạn cần nhập thông tin này';
            }

            if (empty($data['type_order'])) {
                $messages['type_order'] = 'Bạn cần nhập thông tin này';
            } elseif (!is_numeric($data['type_order'])) {
                $messages['type_order'] = 'Bạn cần nhập số';
            }
            $uploadFilesType = array();
            $uploadFilesTeacher = array();

            $error = [];
            $messagesUpload = [];
            $messagesUpload2 = [];
            // Check if the user has uploaded files
            if ($this->request->hasFiles() == true) {
                if ($_FILES['fileUpload-1']['size']) {
                    Upload::uploadFile($uploadFilesType, $messagesUpload, 'fileUpload-1');

                }
                if ($_FILES['fileUpload-2']['size']) {
                    Upload::uploadFile($uploadFilesTeacher, $messagesUpload2, 'fileUpload-2');

                }
            }

            $this->view->uploadFiles = $uploadFilesType;
            if (!empty($uploadFilesType)) {

                $data['type_image'] = $uploadFilesType[0]['file_url'];
            }
            if (!empty($uploadFilesTeacher)) {
                $data['type_icon'] = $uploadFilesTeacher[0]['file_url'];
            }
            if (count($messages) == 0 && empty($messagesUpload)) {
                $msg_result = array();
                $result = $gateway_model->save($data);
                if ($result === true) {
                    $msg_result = array('status' => 'success', 'message' => 'Tạo môn học thành công');

                } else {
                    $message = "We can't store your info now: \n";
                    foreach ($gateway_model->getMessages() as $msg) {
                        $message .= $msg . "\n";
                    }
                    $msg_result['status'] = 'error';
                    $msg_result['message'] = $message;
                }

                $this->session->set('msg_result', $msg_result);
                return $this->response->redirect("/dashboard/list-type-dgnl");
            }
        }
        $messages["status"] = "border-red";
        $data['mode'] = 'Tạo';
        $this->view->setVars([
            'formData' => $data,
            'messages' => $messages,
        ]);
    }
    public function deleteAction()
    {
        $id = $this->request->get('id');
        $msg_result = array('error' => '', 'success' => '');
        $type_model = LearnTypeDgnl::findFirstById($id);
        if ($type_model) {
            if ($type_model->delete() == false) {
                $msg_result['message'] .= 'Không thể xóa môn học: ' . $type_model->getName();
                $msg_result['status'] = 'error';
            } else {
                $message = 'Xóa Môn học ' . $type_model->getName() . ' thành công';
                $msg_result['message'] = $message;
                $msg_result['status'] = 'success';
            }
        }
        $this->session->set('msg_result', $msg_result);
        return $this->response->redirect('/dashboard/list-type-dgnl');
    }

}