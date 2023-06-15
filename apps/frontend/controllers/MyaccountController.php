<?php
namespace Learncom\Frontend\Controllers;


use Learncom\Repositories\Test;
use Learncom\Repositories\User;
use Learncom\Utils\Validator;

class MyaccountController extends ControllerBase
{
    protected $menu;
    protected $item;
    protected $user_id;

    public function indexAction()
    {
        echo "My Account";
}
    public function onConstruct()
    {
        $user = $this->session->get('auth');
        $this->user_id = $user['id'];;
    }
    public function changeprofileAction()
    {
        if (!$this->auth){
            return $this->response->redirect("/login");
        }
        if ($this->session->has('msg_result')) {
            $msg_result = $this->session->get('msg_result');
            $this->session->remove('msg_result');
            $this->view->msg_result = $msg_result;
        }

        $user = new User();
        $user_model = $user->findById($this->user_id);
        if (empty($user_model)) {
            $this->response->redirect('notfound');
            return;
        }
        $data = $user_model->toArray();
        $messages = [];
        $msg_result = [];
        if ($this->request->isPost()) {
            $data['user_name'] = $this->request->getPost('txtName', array('string', 'trim'));
            $data['user_gender'] = $this->request->getPost('slcGender', array('string', 'trim'));
            $data['user_birth_day'] = $this->request->getPost('txtBirthday', array('string', 'trim'));
            $data['user_tel'] = $this->request->getPost('txtTel', array('string', 'trim'));
            $data['user_address'] = $this->request->getPost('txtAddress', array('string', 'trim'));
            if (empty($data['user_name'])) {
                $messages['user_name'] = 'Bạn cần nhập thông tin này';
            }
            if (empty($data['user_gender'])) {
                $messages['user_gender'] = 'Bạn cần nhập thông tin này';
            }

            if ($data['user_birth_day']) {
                $data['user_birth_day'] = strtotime(str_replace('/','-',$data['user_birth_day']));
            }
            if (count($messages) == 0) {
                if (!$data['user_birth_day']) {
                    $data['user_birth_day'] = "";
                }
                $msg_result = array();
                $user_model->setUserName($data['user_name']);
                $user_model->setUserGender($data['user_gender']);
                $user_model->setUserBirthDay($data['user_birth_day']);
                $user_model->setUserTel($data['user_tel']);
                $user_model->setUserAddress($data['user_address']);
                $result = $user_model->save();
                if ($result === true) {
                    $msg_result = array('status' => 'success', 'message' => 'Cập nhật thông tin thành công');

                } else {
                    $message = "Cập nhật thông tin thất bại";
                    $msg_result['status'] = 'danger';
                    $msg_result['message'] = $message;
                }
            }
        }
        $select_gender = User::getComboboxGender($data['user_gender']);
        $messages['status'] = 'border-danger';
        $this->view->setVars([
            'select_gender' => $select_gender,
            'formData' => $data,
            'msg_result' => $msg_result,
            'messages' => $messages,
            'type' => 'changeProfile'
        ]);
    }
    public function changepassAction()
    {
        if ($this->session->has('msg_result')) {
            $msg_result = $this->session->get('msg_result');
            $this->session->remove('msg_result');
            $this->view->msg_result = $msg_result;
        }

        $user = new User();
        $user_model = $user->findById($this->user_id);
        if (empty($user_model)) {
            $this->response->redirect('notfound');
            return;
        }
        $data = $user_model->toArray();
        $messages = [];
        $msg_result = [];
        if ($this->request->isPost()) {
            $data['user_pass'] = $this->request->getPost('txtPass', array('string', 'trim'));
            $data['user_new_pass'] = $this->request->getPost('txtNewPass', array('string', 'trim'));
            $data['user_confirm_pass'] = $this->request->getPost('txtConfirmPass', array('string', 'trim'));
            if (empty($data['user_pass'])) {
                $messages['user_pass'] = 'Bạn cần nhập thông tin này';
            } elseif (strlen($data['user_pass']) < 6) {
                $messages['user_pass'] = 'Mật khẩu phải dài hơn 6 ký tự';
            }
            if (empty($data['user_new_pass'])) {
                $messages['user_new_pass'] = 'Bạn cần nhập thông tin này';
            } elseif (strlen($data['user_new_pass']) < 6) {
                $messages['user_new_pass'] = 'Mật khẩu phải dài hơn 6 ký tự';
            }
            if (empty($data['user_confirm_pass'])) {
                $messages['user_confirm_pass'] = 'Bạn cần nhập thông tin này';
            } elseif (strlen($data['user_confirm_pass']) < 6) {
                $messages['user_confirm_pass'] = 'Mật khẩu phải dài hơn 6 ký tự';
            }
            if (count($messages) == 0) {
                $userPassword = $user_model->getUserPassword();
                if(!$this->security->checkHash($data['user_pass'], $userPassword)){
                    $messages['user_pass'] = 'Mật khẩu không đúng';
                } elseif ( $data['user_new_pass'] != $data['user_confirm_pass']) {
                    $messages['user_confirm_pass'] = 'Mật khẩu xác nhận không đúng';
                }
            }
            if (count($messages) == 0) {
                $msg_result = array();
                $user_model->setUserPassword($data['user_new_pass']);
                $result = $user_model->save();
                if ($result === true) {
                    $msg_result = array('status' => 'success', 'message' => 'Cập nhật mật khẩu thành công');

                } else {
                    $message = "Cập nhật mật khẩu thất bại";
                    $msg_result['status'] = 'danger';
                    $msg_result['message'] = $message;
                }
            }
        }
        $messages['status'] = 'border-danger';
        $this->view->setVars([
            'formData' => $data,
            'msg_result' => $msg_result,
            'messages' => $messages,
            'type' => 'changePass'
        ]);
    }
    public function courseAction() {
        $user = new User();
        $user_model = $user->findById($this->user_id);
        if (empty($user_model)) {
            $this->response->redirect('notfound');
            return;
        }
        $arrClass = explode(',',$user_model->getUserClassIds());
        $arrSubject = explode(',',$user_model->getUserSubjectIds());
        $this->view->setVars([
            'arrClass' => $arrClass,
            'arrSubject' => $arrSubject,
            'type' => 'course'
        ]);
    }
    public function scoreAction() {
        $class_id = $this->request->get('classId');
        $subject_id = $this->request->get('subjectId');
        $arrScore = Test::findScoreByClassAndSubject($class_id,$subject_id);
        $this->view->setVars([
            'arrScore' => $arrScore,
            'type' => 'score'
        ]);
    }
}

