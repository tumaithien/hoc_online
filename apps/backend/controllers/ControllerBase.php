<?php

namespace Learncom\Backend\Controllers;

use Learncom\Models\LearnSchoolClass;
use Learncom\Repositories\Role;

class ControllerBase extends \Phalcon\Mvc\Controller
{
	protected $lang;
	protected $auth;
	protected $isOffice;
	protected $arraySchoolClass;

    public function initialize()
    {
        //current user
        $this->auth = $this->session->get('auth');
        if (isset($this->auth['role'])) {
            $role_function  = array();
            if ($this->session->has('action')) {
                $role_function = $this->session->get('action');
            } else {
                $role = Role::getFirstByName($this->auth['role']);
                if($role) {
                    $role_function = unserialize($role->getRoleActions());
                    $this->session->set('action', $role_function);
                }
            }
            $this->view->role_function = $role_function;
        }
        $this->auth = $this->session->get('auth');
        $this->isOffice = false;
        if(isset($_GET['_url']) && strpos($_GET['_url'],"office")) {
            $this->isOffice = true;
            $this->arraySchoolClass = LearnSchoolClass::find(['order' => "class_name DESC"]);
            $this->view->arraySchoolClass = $this->arraySchoolClass;
        }
        $_SESSION['isOffice'] = $this->isOffice;
        $this->view->isOffice = $this->isOffice;
    }
}
