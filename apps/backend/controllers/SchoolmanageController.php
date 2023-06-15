<?php

namespace Learncom\Backend\Controllers;

use Learncom\Models\LearnGroupSubject;
use Learncom\Models\LearnSchoolClass;
use Learncom\Models\LearnTest;
use Learncom\Models\LearnUser;
use Learncom\Repositories\ClassSubject;
use Learncom\Repositories\Document;
use Learncom\Repositories\SchooManage;
use Learncom\Repositories\Test;
use Learncom\Repositories\User;
use Learncom\Utils\Validator;
use Phalcon\Paginator\Adapter\Model as PaginatorModel;
class SchoolmanageController extends ControllerBase
{
    public function indexAction() {
        $school_class_id = $this->request->get('id');

        $current_page = $this->request->get('page');
        $validator = new Validator();
        if($validator->validInt($current_page) == false || $current_page < 1)
            $current_page=1;
        $sql = "SELECT * FROM Learncom\Models\LearnTest WHERE test_parent_id = 0";
        $arrParameter = array();
        $keyword = $this->request->get('txtSearch');
        if($keyword){
            $sql.=" AND test_name like CONCAT('%',:key_word:,'%') OR test_id = :key_word: ";
            $arrParameter['key_word']=$keyword;
            $this->dispatcher->setParam("txtSearch",$keyword);
        }
        if($school_class_id) {
            $sql .= " AND FIND_IN_SET(:school_class_id:,test_school_class_ids)";
            $arrParameter['school_class_id']=$school_class_id;
            $this->dispatcher->setParam("school_class_id",$school_class_id);
        }
        $sql.=" ORDER BY test_time_start DESC";
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
    public function readAction()
    {
        $school_class_id = $this->request->get('id');
        $test_id = $this->request->get('testId');
        $test_model = Test::findFirstById($test_id);
        $is_special = $this->request->get('is_special');
        if (!$test_model) {
            return $this->response->redirect("/dashboard/accessdenied");
        }
        $test_model = $test_model->getTestParentId() == 0 ? $test_model : Test::findFirstById($test_model->getTestParentId());
        $this->view->school_class_id = $school_class_id;
        $arrTestParent = Test::getArrTestParent($test_id);
        $arrTestId = [];
        foreach ($arrTestParent as $test_parent) {
            array_push($arrTestId,$test_parent->getTestId());
        }
        $strTestId = implode(',',$arrTestId);

        $sql = "SELECT * FROM Learncom\Models\LearnUser as u 
                        INNER JOIN Learncom\Models\LearnScore as s ON u.user_id = s.score_user_id
                        WHERE FIND_IN_SET(s.score_test_id,:arrTest:) AND :school_class: = u.user_school_class_id ";
        $sql_no_start = "SELECT * FROM Learncom\Models\LearnUser as u       
                           WHERE :school_class: = u.user_school_class_id 
                          AND u.user_id NOT IN (SELECT s.score_user_id FROM Learncom\Models\LearnUser as u2 
                        INNER JOIN Learncom\Models\LearnScore as s ON u2.user_id = s.score_user_id
                        WHERE FIND_IN_SET(s.score_test_id,:arrTest:) AND :school_class: = u2.user_school_class_id )";
        $arrParameter = array();
        $arrParameter['arrTest'] = $strTestId;
        $sql_no = " AND s.score_status = 'N' ";
        $sql_pending = " AND s.score_status = 'P' ";
        $sql_success = " AND s.score_status = 'S' ";
        $sql_order = "";
        if ($is_special[0] == "Y") {
            $sql_order .= " AND u.user_special = 'Y'";
        }
        $sql_order .=" AND u.user_school_class_id = :school_class: GROUP BY u.user_id ORDER BY u.user_name ASC";
        $arrParameter['school_class'] = $school_class_id;


        $list_no = $this->modelsManager->executeQuery($sql.$sql_no.$sql_order,$arrParameter);
        $list_no_2 = $this->modelsManager->executeQuery($sql_no_start.$sql_order,$arrParameter);
        $list_pending = $this->modelsManager->executeQuery($sql.$sql_pending.$sql_order,$arrParameter);
        $list_success = $this->modelsManager->executeQuery($sql.$sql_success.$sql_order,$arrParameter);

        if ($this->session->has('msg_result')) {
            $msg_result = $this->session->get('msg_result');
            $this->session->remove('msg_result');
            if ($msg_result) {
                $this->view->msg_result = $msg_result;
            }
        }
        if ($this->request->isAjax()) {
            $tab_2_active = $this->request->getPost('tab_2_active');
            $schooManage = new SchooManage();
            $html_list_no = $schooManage->getHtmlTestNo($list_no,$list_no_2);
            $html_list_pending = $schooManage->getHtmlTestPending($list_pending,$tab_2_active);
            $html_list_success = $schooManage->getHtmlTestSuccess($list_success,$tab_2_active);
            $html_list_alert = $schooManage->getListAlert($list_pending,$tab_2_active);
            $dataReturn = [
                'list_no' => $html_list_no,
                'html_list_pending' => $html_list_pending,
                'html_list_success' => $html_list_success,
                'html_list_alert' => $html_list_alert,
            ];
            die(json_encode($dataReturn));
        } else {
            $this->view->setVars([
                'test_model' => $test_model,
                'list_no' => $list_no,
                'list_no_2' => $list_no_2,
                'list_pending' => $list_pending,
                'list_success' => $list_success,
                'test_id' => $test_id,
                'school_class_id' => $school_class_id,
            ]);
        }
    }


    public function createAction()
    {
        $school_class_id = $this->request->get('class_id');
        $this->view->pick( $this->dispatcher->getControllerName().'/model');
        $data = ['user_order' => 1,'user_class_ids' => "",'user_subject_ids' => "",'user_group_ids' => "",'user_active' => "Y",'user_school_class_id' => ($school_class_id ? $school_class_id : "")];
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
                return $this->response->redirect("/office/list-new-student");
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
                return $this->response->redirect("/office/list-new-student");
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
