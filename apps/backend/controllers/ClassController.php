<?php

namespace Learncom\Backend\Controllers;

use Learncom\Models\LearnClass;
use Learncom\Repositories\ClassSubject;
use Learncom\Utils\Validator;
use Phalcon\Paginator\Adapter\Model as PaginatorModel;
class ClassController extends ControllerBase
{
    public function readAction()
    {
        $current_page = $this->request->get('page');
        $validator = new Validator();
        if($validator->validInt($current_page) == false || $current_page < 1)
            $current_page=1;
        $sql = "SELECT * FROM Learncom\Models\LearnClass WHERE 1";
        $arrParameter = array();

        $sql.=" ORDER BY class_id DESC";
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
        $data = ['class_order' => 1];
        if($this->request->isPost()) {
            $messages = array();
            $data = array(
                'class_name' => $this->request->getPost("txtName", array('string', 'trim')),
                'class_order' => $this->request->getPost("txtOrder", array('string', 'trim')),
            );
            if (empty($data['class_name'])) {
                $messages['class_name'] = 'Bạn cần nhập thông tin này';
            }
            if (empty($data['class_order'])) {
                $messages['class_order'] = 'Bạn cần nhập thông tin này';
            } elseif (!is_numeric($data['class_order'])) {
                $messages['class_order'] = 'Bạn cần nhập số';
            }
            if (count($messages) == 0) {
                $msg_result = array();
                $new_device = new LearnClass();
                $result = $new_device->save($data);
                if ($result === true) {
                    $msg_result = array('status' => 'success', 'message' => 'Tạo lớp học thành công');

                } else {
                    $message = "We can't store your info now: \n";
                    foreach ($new_device->getMessages() as $msg) {
                        $message .= $msg . "\n";
                    }
                    $msg_result['status'] = 'error';
                    $msg_result['message'] = $message;
                }

                $this->session->set('msg_result', $msg_result);
                return $this->response->redirect("/dashboard/list-class");
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
        $gateway_model = ClassSubject::findFirstByClasId($id);
        if(empty($gateway_model))
        {
            return $this->response->redirect('notfound');
        }
        $data = $gateway_model->toArray();
        $messages = array();
        if($this->request->isPost()) {
            $data = array(
                'class_name' => $this->request->getPost("txtName", array('string', 'trim')),
                'class_order' => $this->request->getPost("txtOrder", array('string', 'trim')),
            );
            if (empty($data['class_name'])) {
                $messages['class_name'] = 'Bạn cần nhập thông tin này';
            }
            if (empty($data['class_order'])) {
                $messages['class_order'] = 'Bạn cần nhập thông tin này';
            } elseif (!is_numeric($data['class_order'])) {
                $messages['class_order'] = 'Bạn cần nhập số';
            }
            if (count($messages) == 0) {

                $result = $gateway_model->update($data);
                if ($result === true) {
                    $msg_result = array('status' => 'success', 'message' => 'Cập nhật lớp học thành công');

                } else {
                    $message = "We can't store your info now: \n";
                    foreach ($gateway_model->getMessages() as $msg) {
                        $message .= $msg . "\n";
                    }
                    $msg_result['status'] = 'error';
                    $msg_result['message'] = $message;
                }

                $this->session->set('msg_result', $msg_result);
                return $this->response->redirect("/dashboard/list-class");
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
        $type_model = ClassSubject::findFirstByClasId($id);
        if ($type_model) {
            if(count($message) > 0){
                $msg_result['message'] .= 'Can\'t delete the Class: ' . $type_model->getClassName() . '. Because It\'s exists in: '.$message.'.<br>';
                $msg_result['status'] = 'error';
            }
            else {
                $message = 'Xóa lớp học ' . $type_model->getClassName() . ' thành công';
                $msg_result['message'] = $message;
                $msg_result['status'] = 'success';
                $type_model->delete();
            }
        }
        $this->session->set('msg_result', $msg_result);
        return $this->response->redirect('/dashboard/list-class');
    }

}
