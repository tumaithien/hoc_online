<?php

namespace Learncom\Backend\Controllers;

use Learncom\Models\LearnDgnl;
use Learncom\Models\LearnGroupSubject;
use Learncom\Models\LearnTypeDgnl;
use Learncom\Models\LearnUser;
use Learncom\Repositories\ClassSubject;
use Learncom\Repositories\Document;
use Learncom\Repositories\User;
use Learncom\Utils\Validator;
use Phalcon\Paginator\Adapter\Model as PaginatorModel;

class StudentController extends ControllerBase
{
    public function readAction()
    {
        $current_page = $this->request->get('page');
        $key_word = trim($this->request->get("txtSearchKeyword"));
        $class = trim($this->request->get("slcClass"));
        $subject = trim($this->request->get("slcSubject"));
        $dgnl = trim($this->request->get("dgnl"));
        $validator = new Validator();
        if ($validator->validInt($current_page) == false || $current_page < 1)
            $current_page = 1;
        $sql = "SELECT * FROM Learncom\Models\LearnUser WHERE user_role = 'user'";
        $arrParameter = array();
        if ($key_word) {
            $sql .= " AND (user_name like CONCAT('%',:key_word:,'%')  OR user_email like CONCAT('%',:key_word:,'%') OR user_code like CONCAT('%',:key_word:,'%'))";
            $arrParameter['key_word'] = $key_word;
            $this->dispatcher->setParam("key_word", $key_word);
        }
        if ($class) {
            $sql .= " AND FIND_IN_SET(:class:,user_class_ids)";
            $arrParameter['class'] = $class;
            $this->dispatcher->setParam("slcClass", $class);
        }
        if ($subject) {
            $sql .= " AND FIND_IN_SET(:subject:,user_subject_ids)";
            $arrParameter['subject'] = $subject;
            $this->dispatcher->setParam("slcSubject", $subject);
        }
        if ($dgnl) {
            $sql .= " AND FIND_IN_SET(:dgnl:,user_dgnl_type)";
            $arrParameter['dgnl'] = $dgnl;
            $this->dispatcher->setParam("dgnl", $dgnl);
        }
        $sql .= " ORDER BY user_id DESC";
        $list_gate = $this->modelsManager->executeQuery($sql, $arrParameter);
        $paginator = new PaginatorModel(
            array(
                'data' => $list_gate,
                'limit' => 20,
                'page' => $current_page,
            )
        );
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
        $select_class = ClassSubject::getComboboxClass($class);
        $select_subject = ClassSubject::getComboboxParentSubject('', 0, $subject);
        $select_dgnl = LearnTypeDgnl::getComboboxClass($dgnl);
        $this->view->setVars([
            'list_item' => $paginator->getPaginate(),
            'select_class' => $select_class,
            'select_subject' => $select_subject,
            'select_dgnl' => $select_dgnl
        ]);
    }


    public function createAction()
    {
        $this->view->pick($this->dispatcher->getControllerName() . '/model');
        $data = ['user_order' => 1, 'user_class_ids' => "", 'user_subject_ids' => "", 'user_group_ids' => "", 'user_active' => "Y", 'user_is_public' => "N"];
        if ($this->request->isPost()) {
            $messages = array();
            $data = array(
                'user_name' => $this->request->getPost("txtName", array('string', 'trim')),
                'user_code' => $this->request->getPost("txtCode", array('string', 'trim')),
                'user_email' => $this->request->getPost("txtEmail", array('string', 'trim')),
                'user_ip' => $this->request->getPost("txtIp", array('string', 'trim')),
                'user_password' => $this->request->getPost("txtPassword", array('string', 'trim')),
                'user_re_password' => $this->request->getPost("txtRePassword", array('string', 'trim')),
                'user_description' => trim($this->request->getPost("txtDescription")),
                'user_role' => "user",
                'user_insert_time' => $this->globalVariable->curTime,
                'user_active' => $this->request->getPost("radActive", array('string', 'trim')),
            );
            $arrSubject = [];
            $arrClass = [];
            $arrGroup = [];
            $arrGroupExcept = [];
            if (isset($_POST['slcGroupSubject'])) {
                foreach ($_POST['slcGroupSubject'] as $group_id) {
                    array_push($arrGroup, $group_id);
                }
            }
            if (isset($_POST['slcClass'])) {
                foreach ($_POST['slcClass'] as $class_id) {
                    array_push($arrClass, $class_id);
                }
            }
            $arrType = [];
            if (isset($_POST['slcType'])) {
                foreach ($_POST['slcType'] as $type_id) {
                    array_push($arrType, $type_id);
                }
            }
            if (isset($_POST['slcSubject'])) {
                foreach ($_POST['slcSubject'] as $subject_id) {
                    array_push($arrSubject, $subject_id);
                }
            }
            if (isset($_POST['slcGroup'])) {
                foreach ($_POST['slcGroup'] as $group_id) {
                    array_push($arrGroupExcept, $group_id);
                }
            }
            $data['user_class_ids'] = implode(',', $arrClass);
            $data['user_subject_ids'] = implode(',', $arrSubject);
            $data['user_group_ids'] = implode(',', $arrGroup);
            $data['user_group_excluded_ids'] = implode(',', $arrGroupExcept);
            $data['user_dgnl_type'] = implode(',', $arrType);
            $this->checkValid($data, $messages, -1);
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
                $new_device->setUserGroupIds($data['user_group_ids']);
                $new_device->setUserGroupExcludedIds($data['user_group_excluded_ids']);
                $new_device->setUserDgnlType($data['user_dgnl_type']);

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
                return $this->response->redirect("/dashboard/list-student");
            }
        }
        $messages["status"] = "border-red";
        $data['mode'] = 'Tạo';
        $select_group = ClassSubject::getCheckboxGroupSubject(explode(',', $data['user_group_ids']));
        $select_class = ClassSubject::getCheckboxClass(explode(',', $data['user_class_ids']));
        $select_subject = ClassSubject::getCheckboxSubject(explode(',', $data['user_subject_ids']));
        $select_dgnl = LearnTypeDgnl::getCheckboxDGNL(explode(',', $data['user_dgnl_type']));
        $this->view->setVars([
            'formData' => $data,
            'messages' => $messages,
            'select_group' => $select_group,
            'start' => true,
            'select_class' => $select_class,
            'select_subject' => $select_subject,
            'select_dgnl' => $select_dgnl
        ]);
    }

    public function editAction()
    {
        $this->view->pick($this->dispatcher->getControllerName() . '/model');
        $id = $this->request->get('id');
        $user = new User();
        $gateway_model = $user->findById($id);
        if (empty($gateway_model)) {
            return $this->response->redirect('notfound');
        }
        $data = $gateway_model->toArray();
        $data['user_password'] = $data['user_re_password'] = "password";
        $messages = array();
        if ($this->request->isPost()) {
            $data = array(
                'user_name' => $this->request->getPost("txtName", array('string', 'trim')),
                'user_code' => $this->request->getPost("txtCode", array('string', 'trim')),
                'user_email' => $this->request->getPost("txtEmail", array('string', 'trim')),
                'user_ip' => $this->request->getPost("txtIp", array('string', 'trim')),
                'user_password' => $this->request->getPost("txtPassword", array('string', 'trim')),
                'user_re_password' => $this->request->getPost("txtRePassword", array('string', 'trim')),
                'user_active' => $this->request->getPost("radActive", array('string', 'trim')),
                'user_description' => trim($this->request->getPost("txtDescription")),
            );
            $arrSubject = [];
            $arrClass = [];
            $arrGroup = [];
            $arrGroupExcept = [];
            if (isset($_POST['slcGroupSubject'])) {
                foreach ($_POST['slcGroupSubject'] as $group_id) {
                    array_push($arrGroup, $group_id);
                }
            }
            if (isset($_POST['slcClass'])) {
                foreach ($_POST['slcClass'] as $class_id) {
                    array_push($arrClass, $class_id);
                }
            }
            $arrType = [];
            if (isset($_POST['slcType'])) {
                foreach ($_POST['slcType'] as $type_id) {
                    array_push($arrType, $type_id);
                }
            }
            if (isset($_POST['slcSubject'])) {
                foreach ($_POST['slcSubject'] as $subject_id) {
                    array_push($arrSubject, $subject_id);
                }
            }
            if (isset($_POST['slcGroup'])) {
                foreach ($_POST['slcGroup'] as $group_id) {
                    array_push($arrGroupExcept, $group_id);
                }
            }
            $data['user_group_excluded_ids'] = implode(',', $arrGroupExcept);
            $data['user_class_ids'] = implode(',', $arrClass);
            $data['user_subject_ids'] = implode(',', $arrSubject);
            $data['user_group_ids'] = implode(',', $arrGroup);
            $data['user_dgnl_type'] = implode(',', $arrType);
            $this->checkValid($data, $messages, $gateway_model->getUserId());
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
                $gateway_model->setUserGroupIds($data['user_group_ids']);
                $gateway_model->setUserGroupExcludedIds($data['user_group_excluded_ids']);
                $gateway_model->setUserDgnlType($data['user_dgnl_type']);

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
                return $this->response->redirect("/dashboard/list-student");
            }
        }
        $messages["status"] = "border-red";
        $data['mode'] = 'Chỉnh Sửa';
        $select_group = ClassSubject::getCheckboxGroupSubject(explode(',', $data['user_group_ids']));
        $select_class = ClassSubject::getCheckboxClass(explode(',', $data['user_class_ids']));
        $select_subject = ClassSubject::getCheckboxSubject(explode(',', $data['user_subject_ids']));
        $select_dgnl = LearnTypeDgnl::getCheckboxDGNL(explode(',', $data['user_dgnl_type']));

        $this->view->setVars([
            'formData' => $data,
            'messages' => $messages,
            'select_group' => $select_group,
            'start' => true,
            'select_class' => $select_class,
            'select_subject' => $select_subject,
            'select_dgnl' => $select_dgnl
        ]);
    }
    public function deleteAction()
    {
        $ids = $this->request->get('id');
        $msg_result = [];
        $message = array();
        $arrId = explode(',', $ids);
        $total = 0;
        if (count($arrId)) {
            $user = new User();
            foreach ($arrId as $id) {
                $model = $user->findById($id);
                if ($model) {
                    if ($model->delete()) {
                        $total++;
                    } else {
                        $msg_result['message'] .= 'Can\'t delete  Học sinh ID: ' . $id . '.<br>';
                        $msg_result['status'] = 'error';
                    }
                }
            }

        }
        if ($total > 0) {
            $msg_result['message'] = "Xóa " . $total . " học sinh thành công!!!";
            $msg_result['status'] = 'success';
        }
        $this->session->set('msg_result', $msg_result);
        return $this->response->redirect('/dashboard/list-student');
    }
    public function resetAction()
    {
        $id = $this->request->get('id');
        $mode = $this->request->get('mode');
        $msg_result = [];
        $message = array();
        $user = new User();
        $type_model = $user->findById($id);
        if ($type_model) {
            $type_model->setUserIp("");
            $type_model->setUserTokenCokie1("");
            $type_model->setUserTokenCokie2("");
            if ($type_model->save() == false) {
                $msg_result['message'] .= 'Can\'t reset  Học sinh: ' . $type_model->getUserName() . '. Because It\'s exists in: ' . $message . '.<br>';
                $msg_result['status'] = 'error';
            } else {
                $message = 'Reset Ip cho học sinh: ' . $type_model->getUserName() . ' thành công';
                $msg_result['message'] = $message;
                $msg_result['status'] = 'success';

            }
        }
        $this->session->set('msg_result', $msg_result);
        if ($mode == "office") {
            return $this->response->redirect('/office/list-new-student');
        } else
            return $this->response->redirect('/dashboard/list-student');
    }
    public function checkValid(&$data, &$messages, $id)
    {
        if (empty($data['user_name'])) {
            $messages['user_name'] = 'Bạn cần nhập thông tin này';
        }
        if (empty($data['user_email'])) {
            $messages['user_email'] = 'Bạn cần nhập thông tin này';
        } elseif (user::checkCreateEmail($data['user_email'], $id)) {
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
        } elseif (User::checkCode($data['user_code'], $id)) {
            $messages['user_code'] = 'ID này đã có người sử dụng';
        }

    }
    private function resetAllIp()
    {
        $arrUser = LearnUser::find();
        $total = 0;
        foreach ($arrUser as $user) {
            $user->setUserIp("");
            $user->setUserTokenCokie1("");
            $user->setUserTokenCokie2("");
            if ($user->save()) {
                $total++;
            }
        }
        $msg_result['message'] = "Reset IP cho " . $total . " học sinh hoàn tất";
        $msg_result['status'] = 'success';
        $this->session->set('msg_result', $msg_result);
    }
}