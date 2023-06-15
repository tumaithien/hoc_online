<?php

namespace Learncom\Backend\Controllers;

use Learncom\Models\LearnClass;
use Learncom\Models\LearnMenu;
use Learncom\Models\LearnSubject;
use Learncom\Repositories\ClassSubject;
use Learncom\Repositories\Menu;
use Learncom\Utils\Validator;
use Phalcon\Paginator\Adapter\Model as PaginatorModel;
class MenuController extends ControllerBase
{
    public function readAction()
    {
        $current_page = $this->request->get('page');
        $validator = new Validator();
        if($validator->validInt($current_page) == false || $current_page < 1)
            $current_page=1;
        $sql = "SELECT * FROM Learncom\Models\LearnMenu WHERE 1";
        $arrParameter = array();

        $sql.=" ORDER BY menu_id DESC";
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
        $data = ['menu_order' => 1,'menu_type' => '','menu_active' => "Y",'menu_excluded' => ""];
        $arrIncluded = [];
        if($this->request->isPost()) {
            $messages = array();
            $data = array(
                'menu_name' => $this->request->getPost("txtName", array('string', 'trim')),
                'menu_order' => $this->request->getPost("txtOrder", array('string', 'trim')),
                'menu_type' => $this->request->getPost("slcType", array('string', 'trim')),
                'menu_active' => $this->request->getPost("radActive", array('string', 'trim')),
            );
            if (isset($_POST['dataIncluded']) && count($_POST['dataIncluded']) >0) {
                foreach ($_POST['dataIncluded'] as $data_include) {
                    array_push($arrIncluded,$data_include);
                }
            }
            $data['menu_excluded'] = implode(',',$arrIncluded);
            if (empty($data['menu_name'])) {
                $messages['menu_name'] = 'Bạn cần nhập thông tin này';
            }
            if (empty($data['menu_type'])) {
                $messages['menu_type'] = 'Bạn cần nhập thông tin này';
            }
            if (empty($data['menu_order'])) {
                $messages['menu_order'] = 'Bạn cần nhập thông tin này';
            } elseif (!is_numeric($data['menu_order'])) {
                $messages['menu_order'] = 'Bạn cần nhập số';
            }
            if (count($messages) == 0) {
                $msg_result = array();
                $new_device = new LearnMenu();
                $result = $new_device->save($data);
                if ($result === true) {
                    $msg_result = array('status' => 'success', 'message' => 'Tạo menu thành công');

                } else {
                    $message = "We can't store your info now: \n";
                    foreach ($new_device->getMessages() as $msg) {
                        $message .= $msg . "\n";
                    }
                    $msg_result['status'] = 'error';
                    $msg_result['message'] = $message;
                }

                $this->session->set('msg_result', $msg_result);
                return $this->response->redirect("/dashboard/list-menu");
            }
        }
        $messages["status"] = "border-red";
        $select_type = Menu::getCombobox($data['menu_type']);
        $arrSubject = LearnSubject::find("subject_parent_id = 0");
        $arrClass = LearnClass::find();
        $data['mode'] = 'Tạo';
        $this->view->setVars([
            'formData' => $data,
            'messages' => $messages,
            'select_type' => $select_type,
            'arrSubject' => $arrSubject,
            'arrClass' => $arrClass,
            'arrIncluded' => $arrIncluded
        ]);
    }

    public function editAction()
    {
        $this->view->pick( $this->dispatcher->getControllerName().'/model');
        $id = $this->request->get('id');
        $gateway_model = Menu::findFirstById($id);
        if(empty($gateway_model))
        {
            return $this->response->redirect('notfound');
        }
        $data = $gateway_model->toArray();
        $messages = array();
        $arrIncluded = explode(',',$data['menu_excluded']);
        if($this->request->isPost()) {
            $data = array(
                'menu_name' => $this->request->getPost("txtName", array('string', 'trim')),
                'menu_order' => $this->request->getPost("txtOrder", array('string', 'trim')),
                'menu_type' => $this->request->getPost("slcType", array('string', 'trim')),
                'menu_active' => $this->request->getPost("radActive", array('string', 'trim')),
            );
            $arrIncluded = [];
            if (isset($_POST['dataIncluded']) && count($_POST['dataIncluded']) >0) {
                foreach ($_POST['dataIncluded'] as $data_included) {
                    array_push($arrIncluded,$data_included);
                }
            }
            $data['menu_excluded'] = implode(',',$arrIncluded);
            if (empty($data['menu_name'])) {
                $messages['menu_name'] = 'Bạn cần nhập thông tin này';
            }
            if (empty($data['menu_type'])) {
                $messages['menu_type'] = 'Bạn cần nhập thông tin này';
            }
            if (empty($data['menu_order'])) {
                $messages['menu_order'] = 'Bạn cần nhập thông tin này';
            } elseif (!is_numeric($data['menu_order'])) {
                $messages['menu_order'] = 'Bạn cần nhập số';
            }
            if (count($messages) == 0) {

                $result = $gateway_model->update($data);
                if ($result === true) {
                    $msg_result = array('status' => 'success', 'message' => 'Cập nhật menu thành công');

                } else {
                    $message = "We can't store your info now: \n";
                    foreach ($gateway_model->getMessages() as $msg) {
                        $message .= $msg . "\n";
                    }
                    $msg_result['status'] = 'error';
                    $msg_result['message'] = $message;
                }

                $this->session->set('msg_result', $msg_result);
                return $this->response->redirect("/dashboard/list-menu");
            }
        }
        $messages["status"] = "border-red";
        $select_type = Menu::getCombobox($data['menu_type']);
        $data['mode'] = 'Chỉnh sửa';
        $arrSubject = LearnSubject::find("subject_parent_id = 0");
        $arrClass = LearnClass::find();
        $this->view->setVars([
            'formData' => $data,
            'messages' => $messages,
            'select_type' => $select_type,
            'arrSubject' => $arrSubject,
            'arrClass' => $arrClass,
            'arrIncluded' => $arrIncluded
        ]);
    }
    public function deleteAction()
    {
        $id = $this->request->get('id');
        $msg_result = array('error' => '', 'success' => '');
        $type_model = Menu::findFirstById($id);
        if ($type_model->delete() == false) {
            $msg_result['message'] .= 'Can\'t delete the Class: ' . $type_model->getMenuName();
            $msg_result['status'] = 'error';
        }else {
            $message = 'Xóa Menu ' . $type_model->getMenuName() . ' thành công';
            $msg_result['message'] = $message;
            $msg_result['status'] = 'success';
        }
        $this->session->set('msg_result', $msg_result);
        return $this->response->redirect('/dashboard/list-menu');
    }

}
