<?php

namespace Learncom\Frontend\Controllers;

use Learncom\Models\LearnRole;
use Learncom\Models\LearnUser;
use Learncom\Repositories\Login;
use Learncom\Repositories\Page;
use Learncom\Repositories\User;
use Learncom\Utils\IpApi;

class LoginController extends ControllerBase
{
    public function indexAction()
    {
    }
    public function loginAction()
    {
        if ($this->auth) {
            $this->response->redirect('/');
        }
        $data = array();
        $message = array();
        if ($this->request->isPost()) {
            $data = array(
                'user_code' => $this->request->getPost('txtEmail', array('string', 'trim')),
                'user_password' => $this->request->getPost('txtPassword', array('string', 'trim')),
            );

            if (empty($data['user_code'])) {
                $message['email'] = 'Vui lòng nhập account';
            }
            if (empty($data['user_password'])) {
                $message['password'] = 'Vui lòng nhập mật khẩu';
            }
            $user = LearnUser::findFirstByUserCode($data['user_code']);
            if (!$user) {
                $message['email'] = 'Account này không tồn tại';
            }
            if (empty($message)) {
                $userPassword = $user->getUserPassword();
                if ($this->security->checkHash($data['user_password'], $userPassword)) {
                    if ($user->getUserRole() === 'user') {
                        $checkIp = self::whithStudent($user);
                    } else {
                        $checkIp = true;
                    }
                    $checkIp = true;
                    if ($checkIp) {
                        $this->startSession($user);
                        $preURL = $this->session->get('preURL');
                        // var_dump($preURL);exit;
                        $this->session->remove('preURL');
                        if ($preURL) {
                            $this->response->redirect($preURL);
                        } else {
                            $this->response->redirect("/account/change-profile");
                        }
                    } else {
                        $message['password'] = 'Vui lòng đăng nhập đúng địa chỉ IP';
                    }
                } else {
                    $message['password'] = 'Mật khẩu không đúng';
                }
            }
        }

        $this->view->setVars([
            'formData' => $data,
            'message' => $message,
        ]);
        $repoPage = new Page();
        $repoPage->AutoGenMetaPage('login', 'Login');
    }
    public function registerAction()
    {
        $this->response->redirect('/');

        if ($this->auth) {
            $this->response->redirect('/');
        }
        $data = array();
        $message = array();
        if ($this->request->isPost()) {
            $data = array(
                'user_name' => $this->request->getPost('txtName', array('string', 'trim')),
                'user_code' => $this->request->getPost('txtEmail', array('string', 'trim')),
                'user_password' => $this->request->getPost('txtPassword', array('string', 'trim')),
                'user_password_confirm' => $this->request->getPost('txtPasswordConfirm', array('string', 'trim')),

            );

            $checkUser = User::checkExistCode($data['user_code']);
            if ($checkUser) {
                $message['user_code'] = 'Tài khoản đã có người đăng ký';
            }

            if (empty($data['user_code'])) {
                $message['user_code'] = 'Vui lòng nhập account';
            }
            if (empty($data['user_password'])) {
                $message['user_password'] = 'Vui lòng nhập mật khẩu';
            }
            if (empty($data['user_password_confirm'])) {
                $message['password_confirm'] = 'Vui lòng nhập mật khẩu';
            }
            if (empty($message)) {
                $data['user_active'] = "Y";
                $data['user_spencial'] = "N";
                $data['user_role'] = "user";
                $data['user_insert_time'] = time();

                $user = new LearnUser();
                $result = $user->save($data);
                if ($result) {
                    $user = User::checkExistCode($data['user_code']);
                    $this->startSession($user);
                    $this->response->redirect("/account/change-profile");
                }

            }

        }
        $this->view->setVars([
            'formData' => $data,
            'message' => $message,
        ]);
        $repoPage = new Page();
        $repoPage->AutoGenMetaPage('login', 'Login');
    }
    public function logoutAction()
    {
        $preUrl = $this->session->get('preURL');
        $destoyed = $this->session->destroy();
        setcookie('email', '', time() - 3600);
        setcookie('password', '', time() - 3600);
        $this->session->set('preURL', $preURl);
        $this->response->redirect('/login');
    }

    /**
     * @param LearnUser $user
     */
    private function startSession($user)
    {
        $token_onile = Login::generateKey(30);
        $user->setUserTokenOnline($token_onile);
        $result = $user->save();
        if ($result) {
            $role_name = $user->getUserRole();
            $user_repo = new User();
            $user_repo->initSession($user, $role_name, $token_onile);
            $this->auth = $this->session->get('auth');
            $_SESSION['ip'] = $_SERVER['REMOTE_ADDR'];
        }
    }
    private static function whithStudent($user)
    {
        $checkIp = false;
        $externalIp = $_SERVER['REMOTE_ADDR'];
        $userIp = $user->getUserIp();
        $cookie_1 = isset($_COOKIE['p1']) ? $_COOKIE['p1'] : "";
        $cookie_2 = isset($_COOKIE['p2']) ? $_COOKIE['p2'] : "";
        $token_cookie_1 = $user->getUserTokenCokie1();
        $token_cookie_2 = $user->getUserTokenCokie2();
        if ($userIp && strlen($userIp)) {
            if ($userIp == $externalIp) {
                $checkIp = true;
            }
        } else {
            $user->setUserIp($externalIp);
            $resultSave = $user->save();
            if ($resultSave) {
                $checkIp = true;
            }
        }
        if ($checkIp) {
            if (!$token_cookie_1) {
                $token_1 = Login::generateKey(30);
                $user->setUserTokenCokie1($token_1);
                $result1 = $user->save();
                if ($result1) {
                    //cokie 1 năm
                    setcookie('p1', $token_1, time() + 1 * 365 * 24 * 60 * 60, "/");
                }
            } else {
                $token_2 = Login::generateKey(30);
                $user->setUserTokenCokie2($token_2);
                $result2 = $user->save();
                if ($result2) {
                    //cokie 1 năm
                    setcookie('p2', $token_2, time() + 1 * 365 * 24 * 60 * 60, "/");
                }
            }
        } else {
            if ($cookie_1) {
                if ($cookie_1 == $token_cookie_1) {
                    $checkIp = true;
                }
            }
            if ($cookie_2) {
                if ($cookie_2 == $token_cookie_2) {
                    $checkIp = true;
                }
            }
        }
        return $checkIp;
    }
}