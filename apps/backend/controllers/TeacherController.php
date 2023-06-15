<?php

namespace Learncom\Backend\Controllers;

use Learncom\Models\LearnUser;
use Learncom\Repositories\ClassSubject;
use Learncom\Repositories\Document;
use Learncom\Repositories\User;
use Learncom\Utils\Validator;
use Phalcon\Paginator\Adapter\Model as PaginatorModel;
class TeacherController extends ControllerBase
{
    public function readAction()
    {
        $current_page = $this->request->get('page');
        $key_word=trim($this->request->get("txtSearchKeyword"));
        $class=trim($this->request->get("slcClass"));
        $subject=trim($this->request->get("slcSubject"));
        $validator = new Validator();
        if($validator->validInt($current_page) == false || $current_page < 1)
            $current_page=1;
        $sql = "SELECT * FROM Learncom\Models\LearnUser WHERE user_role = 'teacher'";
        $arrParameter = array();
        if($key_word){
            $sql.=" AND (user_name like CONCAT('%',:key_word:,'%')  OR user_email like CONCAT('%',:key_word:,'%') OR user_code like CONCAT('%',:key_word:,'%'))";
            $arrParameter['key_word']=$key_word;
            $this->dispatcher->setParam("key_word",$key_word);
        }
        if ($class) {
            $sql.= " AND FIND_IN_SET(:class:,user_class_ids)";
            $arrParameter['class']=$class;
            $this->dispatcher->setParam("slcClass",$class);
        }
        if ($subject) {
            $sql.= " AND FIND_IN_SET(:subject:,user_subject_ids)";
            $arrParameter['subject']=$subject;
            $this->dispatcher->setParam("slcSubject",$subject);
        }
        $sql.=" ORDER BY user_id DESC";
        $list_gate = $this->modelsManager->executeQuery($sql,$arrParameter);
        $paginator = new PaginatorModel(array(
            'data'  => $list_gate,
            'limit' => 20,
            'page'  => $current_page,
        ));
        if ($this->session->has('msg_result')) {
            $msg_result = $this->session->get('msg_result');
            $this->session->remove('msg_result');
            if ($msg_result) {
                $this->view->msg_result = $msg_result;
            }
        }
        $select_class = ClassSubject::getComboboxClass($class);
        $select_subject = ClassSubject::getComboboxParentSubject('',0,$subject);
        $this->view->setVars([
            'list_item' => $paginator->getPaginate(),
            'select_class' => $select_class,
            'select_subject' => $select_subject
        ]);
    }


    public function createAction()
    {
        $this->view->pick( $this->dispatcher->getControllerName().'/model');
        $data = ['user_order' => 1,'user_class_ids' => "",'user_subject_ids' => "",'user_active' => "Y",'user_is_public' => "N"];
        if($this->request->isPost()) {
            $messages = array();
            $data = array(
                'user_name' => $this->request->getPost("txtName", array('string', 'trim')),
                'user_code' => $this->request->getPost("txtCode", array('string', 'trim')),
                'user_email' => $this->request->getPost("txtEmail", array('string', 'trim')),
                'user_ip' => $this->request->getPost("txtIp", array('string', 'trim')),
                'user_password' => $this->request->getPost("txtPassword", array('string', 'trim')),
                'user_re_password' => $this->request->getPost("txtRePassword", array('string', 'trim')),
                'user_role' => "teacher",
                'user_insert_time' => $this->globalVariable->curTime,
                'user_active' => $this->request->getPost("radActive", array('string', 'trim')),
                'user_description' => trim($this->request->getPost("txtDescription")),
                'user_address' => trim($this->request->getPost("txtAddress")),
            );
            $arrSubject  = [];
            $arrClass  = [];
            if (isset($_POST['slcClass'])) {
                foreach ($_POST['slcClass'] as $class_id) {
                    array_push($arrClass,$class_id);
                }
            }
            if (isset($_POST['slcSubject'])) {
                foreach ($_POST['slcSubject'] as $subject_id) {
                    array_push($arrSubject,$subject_id);
                }
            }
            $data['user_class_ids'] = implode(',',$arrClass);
            $data['user_subject_ids'] = implode(',',$arrSubject);
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
                $new_device->setUserClassIds($data['user_class_ids']);
                $new_device->setUserSubjectIds($data['user_subject_ids']);
                $new_device->setUserActive($data['user_active']);
                $new_device->setUserDescription($data['user_description']);
                $new_device->setUserAddress($data['user_address']);
                $result = $new_device->save();
                if ($result === true) {
                    $msg_result = array('status' => 'success', 'message' => 'Tạo giáo viên thành công');

                } else {
                    $message = "We can't store your info now: \n";
                    foreach ($new_device->getMessages() as $msg) {
                        $message .= $msg . "\n";
                    }
                    $msg_result['status'] = 'error';
                    $msg_result['message'] = $message;
                }

                $this->session->set('msg_result', $msg_result);
                return $this->response->redirect("/dashboard/list-teacher");
            }
        }
        $messages["status"] = "border-red";
        $data['mode'] = 'Tạo';
        $select_class = ClassSubject::getCheckboxClass(explode(',',$data['user_class_ids']));
        $select_subject = ClassSubject::getCheckboxSubject(explode(',',$data['user_subject_ids']));
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
                'user_address' => trim($this->request->getPost("txtAddress")),
            );
            $arrSubject  = [];
            $arrClass  = [];
            if (isset($_POST['slcClass'])) {
                foreach ($_POST['slcClass'] as $class_id) {
                    array_push($arrClass,$class_id);
                }
            }
            if (isset($_POST['slcSubject'])) {
                foreach ($_POST['slcSubject'] as $subject_id) {
                    array_push($arrSubject,$subject_id);
                }
            }
            $data['user_class_ids'] = implode(',',$arrClass);
            $data['user_subject_ids'] = implode(',',$arrSubject);
            $this->checkValid($data,$messages,$gateway_model->getUserId());
            if (count($messages) == 0) {
                if ($data['user_password'] != "password") {
                    $gateway_model->setUserPassword($data['user_password']);
                }
                $gateway_model->setUserName($data['user_name']);
                $gateway_model->setUserEmail($data['user_email']);
                $gateway_model->setUserIp($data['user_ip']);
                $gateway_model->setUserClassIds($data['user_class_ids']);
                $gateway_model->setUserSubjectIds($data['user_subject_ids']);
                $gateway_model->setUserActive($data['user_active']);
                $gateway_model->setUserCode($data['user_code']);
                $gateway_model->setUserDescription($data['user_description']);
                $gateway_model->setUserAddress($data['user_address']);
                $result = $gateway_model->update();
                if ($result === true) {
                    $msg_result = array('status' => 'success', 'message' => 'Cập nhật giáo viên hoàn tất');

                } else {
                    $message = "We can't store your info now: \n";
                    foreach ($gateway_model->getMessages() as $msg) {
                        $message .= $msg . "\n";
                    }
                    $msg_result['status'] = 'error';
                    $msg_result['message'] = $message;
                }

                $this->session->set('msg_result', $msg_result);
                return $this->response->redirect("/dashboard/list-teacher");
            }
        }
        $messages["status"] = "border-red";
        $data['mode'] = 'Chỉnh Sửa';
        $select_class = ClassSubject::getCheckboxClass(explode(',',$data['user_class_ids']));
        $select_subject = ClassSubject::getCheckboxSubject(explode(',',$data['user_subject_ids']));
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
        $msg_result = [];
        $message = array();
        $user= new User();
        $type_model = $user->findById($id);
        if ($type_model) {
            if(count($message) > 0){
                $msg_result['message'] .= 'Can\'t delete  giáo viên: ' . $type_model->getUserName() . '. Because It\'s exists in: '.$message.'.<br>';
                $msg_result['status'] = 'error';
            }
            else {
                $message = 'Xóa giáo viên ' . $type_model->getUserName() . ' thành công';
                $msg_result['message'] = $message;
                $msg_result['status'] = 'success';
                $type_model->delete();
            }
        }
        $this->session->set('msg_result', $msg_result);
        return $this->response->redirect('/dashboard/list-teacher');
    }
  
    public function checkValid(&$data,&$messages,$id) {
        if (empty($data['user_name'])) {
            $messages['user_name'] = 'Bạn cần nhập thông tin này';
        }
        if (empty($data['user_email'])) {
            $messages['user_email'] = 'Bạn cần nhập thông tin này';
        } elseif (user::checkCreateEmail($data['user_email'],$id)) {
            $messages['user_email'] = 'Email này đã có người đăng ký';
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
