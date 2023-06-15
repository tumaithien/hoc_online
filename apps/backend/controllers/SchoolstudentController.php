<?php

namespace Learncom\Backend\Controllers;

use Learncom\Models\LearnGroupSubject;
use Learncom\Models\LearnSchoolClass;
use Learncom\Models\LearnUser;
use Learncom\Repositories\ClassSubject;
use Learncom\Repositories\Document;
use Learncom\Repositories\User;
use Learncom\Utils\Validator;
use Phalcon\Paginator\Adapter\Model as PaginatorModel;
class SchoolstudentController extends ControllerBase
{
    public function readAction()
    {
        $current_page = $this->request->get('page');
        $key_word=trim($this->request->get("txtSearchKeyword"));
        $school_class_id=trim($this->request->get("slcClass"));
        $school_class_id = $school_class_id ? $school_class_id : "all";
        $this->view->school_class_id = $school_class_id;
        $validator = new Validator();
        if($validator->validInt($current_page) == false || $current_page < 1)
            $current_page=1;
        $sql = "SELECT * FROM Learncom\Models\LearnUser WHERE user_role = 'user'";
        $arrParameter = array();
        if($key_word){
            $sql.=" AND (user_name like CONCAT('%',:key_word:,'%')  OR user_email like CONCAT('%',:key_word:,'%') OR user_code like CONCAT('%',:key_word:,'%'))";
            $arrParameter['key_word']=$key_word;
            $this->dispatcher->setParam("txtSearchKeyword",$key_word);
        }
        if ($school_class_id !== "all" && $school_class_id != "special") {
            $sql.= " AND user_school_class_id = :class:";
            $arrParameter['class']=$school_class_id;
            $this->dispatcher->setParam("slcClass",$school_class_id);
        }
        if ($school_class_id == "special") {
            $sql .= " AND user_special = 'N'";
        }

        $sql.=" ORDER BY user_school_class_id DESC";
        $list_gate = $this->modelsManager->executeQuery($sql,$arrParameter);
        $paginator = new PaginatorModel(array(
            'data'  => $list_gate,
            'limit' => 20,
            'page'  => $current_page,
        ));
        $reset_all_ip = $this->request->getPost('resetAllIp');
        if ($reset_all_ip == "resetAllIp") {
            $this->resetAllIp();
        }
        if ($this->session->has('msg_result')) {
            $msg_result = $this->session->get('msg_result');
            $this->session->remove('msg_result');
            if ($msg_result) {
                $this->view->msg_result = $msg_result;
            }
        }
        $select_school_class = LearnSchoolClass::getCombobox($school_class_id);

        $this->view->setVars([
            'list_item' => $paginator->getPaginate(),
            'select_school_class' => $select_school_class
        ]);
    }


    public function createAction()
    {
        $school_class_id = $this->request->get('class_id');
        $this->view->pick( $this->dispatcher->getControllerName().'/model');
        $data = ['user_order' => 1,'user_class_ids' => "",'user_subject_ids' => "",'user_group_ids' => "",
            'user_active' => "Y",'user_school_class_id' => ($school_class_id ? $school_class_id : ""),'user_special'=>"N"];
        if($this->request->isPost()) {
            $messages = array();
            $data = array(
                'user_name' => $this->request->getPost("txtName", array('string', 'trim')),
                'user_code' => $this->request->getPost("txtCode", array('string', 'trim')),
                'user_email' => $this->request->getPost("txtEmail", array('string', 'trim')),
                'user_ip' => $this->request->getPost("txtIp", array('string', 'trim')),
                'user_password' => $this->request->getPost("txtPassword", array('string', 'trim')),
                'user_re_password' => $this->request->getPost("txtRePassword", array('string', 'trim')),
                'user_description' => trim($this->request->getPost("txtDescription")),
                'user_school_class_id' => $this->request->getPost("slcSchoolClass", array('string', 'trim')),
                'user_role' => "user",
                'user_insert_time' => $this->globalVariable->curTime,
                'user_active' => $this->request->getPost("radActive", array('string', 'trim')),
                'user_special' => $this->request->getPost("radSpecial", array('string', 'trim')),

            );
            $this->checkValid($data,$messages,-1);
            if (count($messages) == 0) {
                $msg_result = array();
                $new_device = new LearnUser();
                $new_device->setUserName($data['user_name']);
                $new_device->setUserPassword($data['user_password']);
                $new_device->setUserCode($data['user_code']);
                $new_device->setUserEmail($data['user_email']);
                $new_device->setUserIp($data['user_ip']);
                $new_device->setUserRole($data['user_role']);
                $new_device->setUserInsertTime($data['user_insert_time']);
                $new_device->setUserActive($data['user_active']);
                $new_device->setUserDescription($data['user_description']);
                $new_device->setUserSchoolClassId($data['user_school_class_id']);
                $new_device->setUserSpecial($data['user_special']);

                $result = $new_device->save();
                if ($result === true) {
                    $msg_result = array('status' => 'success', 'message' => 'Tạo học sinh thành công');

                } else {
                    $message = "We can't store your info now: \n";
                    foreach ($new_device->getMessages() as $msg) {
                        $message .= $msg . "\n";
                    }
                    $msg_result['status'] = 'error';
                    $msg_result['message'] = $message;
                }

                $this->session->set('msg_result', $msg_result);
                return $this->response->redirect("/office/list-new-student?slcClass=".$data['user_school_class_id']);
            }
        }
        $select_school_class = LearnSchoolClass::getCombobox($data['user_school_class_id']);
        $messages["status"] = "border-red";
        $data['mode'] = 'Tạo';
        $this->view->setVars([
            'formData' => $data,
            'messages' => $messages,
            'select_school_class' => $select_school_class,
            'start' => true,
        ]);
    }

    public function editAction()
    {
        $this->view->pick( $this->dispatcher->getControllerName().'/model');
        $id = $this->request->get('id');
        $user = new User();
        $gateway_model = $user->findById($id);
        if(empty($gateway_model))
        {
            return $this->response->redirect('notfound');
        }
        $data = $gateway_model->toArray();
        $data['user_password'] = $data['user_re_password'] = "password";
        $messages = array();
        if($this->request->isPost()) {
            $data = array(
                'user_name' => $this->request->getPost("txtName", array('string', 'trim')),
                'user_code' => $this->request->getPost("txtCode", array('string', 'trim')),
                'user_email' => $this->request->getPost("txtEmail", array('string', 'trim')),
                'user_ip' => $this->request->getPost("txtIp", array('string', 'trim')),
                'user_password' => $this->request->getPost("txtPassword", array('string', 'trim')),
                'user_re_password' => $this->request->getPost("txtRePassword", array('string', 'trim')),
                'user_active' => $this->request->getPost("radActive", array('string', 'trim')),
                'user_description' => trim($this->request->getPost("txtDescription")),
                'user_school_class_id' => $this->request->getPost("slcSchoolClass", array('string', 'trim')),
                'user_special' => $this->request->getPost("radSpecial", array('string', 'trim')),

            );
            $this->checkValid($data,$messages,$gateway_model->getUserId());
            if (count($messages) == 0) {
                if ($data['user_password'] != "password") {
                    $gateway_model->setUserPassword($data['user_password']);
                }
                $gateway_model->setUserName($data['user_name']);
                $gateway_model->setUserEmail($data['user_email']);
                $gateway_model->setUserIp($data['user_ip']);
                $gateway_model->setUserActive($data['user_active']);
                $gateway_model->setUserCode($data['user_code']);
                $gateway_model->setUserDescription($data['user_description']);
                $gateway_model->setUserSchoolClassId($data['user_school_class_id']);
                $gateway_model->setUserSpecial($data['user_special']);

                $result = $gateway_model->update();
                if ($result === true) {
                    $msg_result = array('status' => 'success', 'message' => 'Cập nhật học sinh hoàn tất');

                } else {
                    $message = "We can't store your info now: \n";
                    foreach ($gateway_model->getMessages() as $msg) {
                        $message .= $msg . "\n";
                    }
                    $msg_result['status'] = 'error';
                    $msg_result['message'] = $message;
                }

                $this->session->set('msg_result', $msg_result);
                return $this->response->redirect("/office/list-new-student?slcClass=".$data['user_school_class_id']);
            }
        }
        $select_school_class = LearnSchoolClass::getCombobox($data['user_school_class_id']);
        $messages["status"] = "border-red";
        $data['mode'] = 'Chỉnh Sửa';
        $this->view->setVars([
            'formData' => $data,
            'messages' => $messages,
            'start' => true,
            'select_school_class' => $select_school_class
        ]);
    }
    public function deleteAction()
    {
        $items_checked = $this->request->getPost("item");


        if (!empty($items_checked)) {
            $msg_result = array();
            $count_delete = 0;
            $user= new User();
            foreach ($items_checked as $id) {
                $item = $user->findById($id);

                if ($item) {
                    if ($item->delete() === false) {
                        $message_delete = 'Can\'t delete the student name = ' . $item->getUserName();
                        $msg_result['status'] = 'error';
                        $msg_result['msg'] = $message_delete;
                    } else {
                        $count_delete++;
                        $user->deleteUser($id);
                    }
                }
            }
            if ($count_delete > 0) {
                $message_delete = 'Delete ' . $count_delete . ' student successfully.';
                $msg_result['status'] = 'success';
                $msg_result['message'] = $message_delete;
            }
            $this->session->set('msg_result', $msg_result);
            return $this->response->redirect('/office/list-new-student');
        }

    }
    public function checkValid(&$data,&$messages,$id) {
        if (empty($data['user_name'])) {
            $messages['user_name'] = 'Bạn cần nhập thông tin này';
        }
        if (empty($data['user_password'])) {
            $messages['user_password'] = 'Bạn cần nhập thông tin này';
        } elseif (strlen($data['user_password']) < 6) {
            $messages['user_password'] = 'Mật khẩu phải lớn hơn 6 ký tự';
        }
        if (empty($data['user_re_password'])) {
            $messages['user_re_password'] = 'Bạn cần nhập thông tin này';
        } elseif (strlen($data['user_re_password']) < 6) {
            $messages['user_re_password'] = 'Mật khẩu phải lớn hơn 6 ký tự';
        } elseif ($data['user_re_password'] != $data['user_password']) {
            $messages['user_re_password'] = 'Mật khẩu xác nhận không đúng';
        }
        if (empty($data['user_code'])) {
            $messages['user_code'] = 'Bạn cần nhập thông tin này';
        } elseif (User::checkCode($data['user_code'],$id)) {
            $messages['user_code'] = 'ID này đã có người sử dụng';
        }

    }
}
