<?php

namespace Learncom\Backend\Controllers;

use Learncom\Models\LearnDgnl;
use Learncom\Models\LearnTypeDgnl;
use Learncom\Repositories\ClassSubject;
use Learncom\Repositories\Video;
use Learncom\Utils\Validator;
use Phalcon\Paginator\Adapter\Model as PaginatorModel;

class DgnlController extends ControllerBase
{
    public function readAction()
    {
        $current_page = $this->request->get('page');
        $key_word = trim($this->request->get("txtSearchKeyword"));
        $type = trim($this->request->get("slcType"));
        $validator = new Validator();
        if ($validator->validInt($current_page) == false || $current_page < 1)
            $current_page = 1;
        $sql = "SELECT * FROM Learncom\Models\LearnDgnl WHERE 1";
        $arrParameter = array();
        if ($key_word) {
            $sql .= " AND dgnl_name like CONCAT('%',:key_word:,'%') OR dgnl_id = :key_word: ";
            $arrParameter['key_word'] = $key_word;
            $this->dispatcher->setParam("key_word", $key_word);
        }

        if ($type) {
            $sql .= " AND dgnl_type_id = :type:";
            $arrParameter['type'] = $type;
            $this->dispatcher->setParam("slcType", $type);
        }

        $sql .= " ORDER BY dgnl_id DESC";
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
        $select_type = LearnTypeDgnl::getComboboxClass($type);
        $this->view->setVars([
            'list_item' => $paginator->getPaginate(),
            'select_type' => $select_type,
        ]);
    }


    public function createAction()
    {
        $this->view->pick($this->dispatcher->getControllerName() . '/model');
        $data = ['dgnl_order' => 1, 'dgnl_class_id' => "", 'dgnl_subject_id' => "", 'dgnl_active' => "Y"];
        if ($this->request->isPost()) {
            $messages = array();
            $data = array(
                'dgnl_name' => $this->request->getPost("txtName", array('string', 'trim')),
                'dgnl_type' => $this->request->getPost("slcType", array('string', 'trim')),
                'dgnl_order' => $this->request->getPost("txtOrder", array('string', 'trim')),
                'dgnl_link' => $this->request->getPost("txtLink", array('string', 'trim')),
                'dgnl_type_id' => $this->request->getPost("slcType", array('string', 'trim')),
                'learn_category' => $this->request->getPost("slcCategory", array('string', 'trim')),
                'dgnl_group_id' => $this->request->getPost("slcGroup", array('string', 'trim')),
            );

            if (empty($data['dgnl_name'])) {
                $messages['dgnl_name'] = 'Bạn cần nhập thông tin này';
            }
            if (empty($data['dgnl_link'])) {
                $messages['dgnl_link'] = 'Bạn cần nhập thông tin này';
            }
            if (empty($data['dgnl_type_id'])) {
                $messages['dgnl_type_id'] = 'Bạn cần nhập thông tin này';
            }
          
            if (empty($data['dgnl_order'])) {
                $messages['dgnl_order'] = 'Bạn cần nhập thông tin này';
            } elseif (!is_numeric($data['dgnl_order'])) {
                $messages['dgnl_order'] = 'Bạn cần nhập số';
            }
            if (count($messages) == 0) {
                $msg_result = array();
                $new_device = new LearnDgnl();
              
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
                return $this->response->redirect("/dashboard/list-dgnl");
            }
        }
        $messages["status"] = "border-red";
        $data['mode'] = 'Tạo';
        $select_type = LearnTypeDgnl::getComboboxClass($data['dgnl_type_id']);
        $this->view->setVars([
            'formData' => $data,
            'messages' => $messages,
            'select_type' => $select_type,
        ]);
    }

    public function editAction()
    {
        $this->view->pick($this->dispatcher->getControllerName() . '/model');
        $id = $this->request->get('id');
        $gateway_model = LearnDgnl::findFirstById($id);
        if (empty($gateway_model)) {
            return $this->response->redirect('notfound');
        }
        $data = $gateway_model->toArray();
        $messages = array();
        if ($this->request->isPost()) {
            $data = array(
                'dgnl_name' => $this->request->getPost("txtName", array('string', 'trim')),
                'dgnl_type' => $this->request->getPost("slcType", array('string', 'trim')),
                'dgnl_order' => $this->request->getPost("txtOrder", array('string', 'trim')),
                'dgnl_link' => $this->request->getPost("txtLink", array('string', 'trim')),
                'dgnl_type_id' => $this->request->getPost("slcType", array('string', 'trim')),
                'learn_category' => $this->request->getPost("slcCategory", array('string', 'trim')),
                'dgnl_group_id' => $this->request->getPost("slcGroup", array('string', 'trim')),
            );

         
            if (empty($data['dgnl_name'])) {
                $messages['dgnl_name'] = 'Bạn cần nhập thông tin này';
            }
            if (empty($data['dgnl_link'])) {
                $messages['dgnl_link'] = 'Bạn cần nhập thông tin này';
            }
            if (empty($data['dgnl_type_id'])) {
                $messages['dgnl_type_id'] = 'Bạn cần nhập thông tin này';
            }
          
            if (empty($data['dgnl_order'])) {
                $messages['dgnl_order'] = 'Bạn cần nhập thông tin này';
            } elseif (!is_numeric($data['dgnl_order'])) {
                $messages['dgnl_order'] = 'Bạn cần nhập số';
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
                return $this->response->redirect("/dashboard/list-dgnl");
            }
        }
        $messages["status"] = "border-red";
        $data['mode'] = 'Tạo';
        $select_type = LearnTypeDgnl::getComboboxClass($data['dgnl_type_id']);
        $this->view->setVars([
            'formData' => $data,
            'messages' => $messages,
            'select_type' => $select_type,
        ]);
    }
    public function deleteAction()
    {
        $id = $this->request->get('id');
        $msg_result = array('error' => '', 'success' => '');
        $type_model = LearnDgnl::findFirstById($id);
        if ($type_model) {
            if ($type_model->delete() == false) {
                $msg_result['message'] .= 'Can\'t delete the Video: ' . $type_model->getName();
                $msg_result['status'] = 'error';
            } else {
                $message = 'Xóa Video ' . $type_model->getName() . ' thành công';
                $msg_result['message'] = $message;
                $msg_result['status'] = 'success';
            }
        }
        $this->session->set('msg_result', $msg_result);
        return $this->response->redirect('/dashboard/list-dgnl');
    }

}